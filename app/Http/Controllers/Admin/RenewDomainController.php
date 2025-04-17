<?php

namespace App\Http\Controllers\Admin;

use AfriCC\EPP\Frame\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DomainRenewalRequest;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Domain;
use App\Services\Epp\EppService;
use Carbon\Carbon;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class RenewDomainController extends Controller
{
    protected EppService $eppService;
    public $cartPeriodCount = 1;
    public $cartTotal = 1;
    public $periode = 1;
    public $domainId = '';
    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    public function index(string $uuid): View|RedirectResponse
    {
        $domain = Domain::where('uuid', $uuid)->firstOrFail();
        $this->domainId = 'renew_' . $domain->name;
        // Check if user owns the domain
        if ($domain->owner_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You do not have permission to renew this domain.');
        }

        $cartItems = Cart::getContent();
        $total = Cart::getTotal();
        $this->cartTotal = $total;
        if (Cart::get($this->domainId)) {
            $cartitm=Cart::get($this->domainId);
           // dd($cartitm);
            $this->periode = $cartitm['quantity'];
        }
        // Get all contacts for the current user with essential fields
        $contacts = Contact::where('user_id', Auth::id())
            ->select('id', 'contact_id', 'name', 'organization', 'email', 'voice')
            ->orderBy('created_at', 'desc')
            ->get();

        $countries = Country::all();

        // Try to get domain information from EPP
        try {
            $eppInfo = $this->eppService->getDomainInfo($domain->name);
            //dd($eppInfo);
            // dd($eppInfo['registrant']);
            // foreach ($contacts as $contact) {
            //     if ($contact->contact_id === $eppInfo['registrant']) {
            //         echo $contact->contact_id . ' = ' . $eppInfo['registrant'].'<br>';
            //     }else{
            //         echo $contact->contact_id . ' != ' . $eppInfo['registrant'].'<br>';
            //     }
            // }
            // Log the raw response for debugging
            Log::debug('Registry response for domain info', [
                'domain' => $domain->name,
                'response' => $eppInfo,
            ]);

            if ($eppInfo && isset($eppInfo['infData'])) {
                $eppInfo = $eppInfo['infData'];

                // Update domain with registry data if available
                if (isset($eppInfo['exDate'])) {
                    $expiryDate = Carbon::parse($eppInfo['exDate']);
                    $domain->update(['expires_at' => $expiryDate]);
                    $domain->refresh();

                    Log::info('Updated domain with registry data', [
                        'domain' => $domain->name,
                        'registry_expiry' => $eppInfo['exDate'],
                        'db_expiry' => $domain->expires_at,
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::warning('EPP service warning - continuing with renewal process', [
                'domain' => $domain->name,
                'error' => $e->getMessage(),
            ]);
            $eppInfo = [];
        }

        return view('admin.domains.renewal', [
            'domain' => $domain,
            'cartItems' => $cartItems,
            'total' => $total,
            'contacts' => $contacts,
            'countries' => $countries,
            'eppInfo' => $eppInfo ?? [],
        ]);
    }

    /**
     * Add a domain to the cart for renewal
     */
    public function addToCart(string $uuid): RedirectResponse
    {
        $domain = Domain::where('uuid', $uuid)->firstOrFail();

        // Check if user owns the domain
        if ($domain->owner_id !== auth()->id()) {
            return redirect()->route('admin.domains.index')
                ->with('error', 'You are not authorized to renew this domain.');
        }

        try {
            // Check if domain is already in cart
            $cartContent = Cart::getContent();
            $cartItemId = 'renew_' . $domain->name;

            if ($cartContent->has($cartItemId)) {
                return redirect()->route('admin.domains.renewal.index', ['uuid' => $domain->uuid])
                    ->with('warning', 'Domain renewal is already in your cart.');
            }

            // Get renewal price from domain pricing
            $renewPrice = $domain->domainPricing->renew_price;

            if (! $renewPrice) {
                return redirect()->route('admin.domains.index')
                    ->with('error', 'Renewal price not found for this domain.');
            }

            // Add to cart with proper attributes
            Cart::add([
                'id' => $cartItemId,
                'name' => $domain->name,
                'price' => $renewPrice,
                'quantity' => 1,
                'attributes' => [
                    'domain' => $domain->name,
                    'type' => 'renewal',
                    'user_id' => auth()->id(),
                    'domain_id' => $domain->id,
                ],
                'associatedModel' => Domain::class,
            ]);

            Log::debug('Domain renewal added to cart:', [
                'domain' => $domain->name,
                'price' => $renewPrice,
                'cart_id' => $cartItemId,
            ]);

            return redirect()->route('admin.domains.renewal.index', ['uuid' => $domain->uuid])
                ->with('success', 'Domain renewal added to cart successfully.');
        } catch (Exception $e) {
            Log::error('Failed to add domain renewal to cart:', [
                'domain' => $domain->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.domains.index')
                ->with('error', 'Failed to add domain renewal to cart. ' . $e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function renew(DomainRenewalRequest $request, string $uuid): RedirectResponse
    {
        $domain = Domain::where('uuid', $uuid)->firstOrFail();

        if ($domain->owner_id !== auth()->user()->id) {
            return redirect()->route('admin.domains.index')->with('error', 'You are not allowed to renew this domain.');
        }

        try {
            DB::beginTransaction();
            try {
                // Check if domain exists in cart
                $cartItemId = 'renew_' . $domain->name;
                if (! Cart::get($cartItemId)) {
                    throw new Exception('Domain renewal not found in cart. Please add it to cart first.');
                }

                /**
                 * Get current domain information from registry
                 * This is critical for renewal as we need the exact expiry date
                 */
                try {
                    // Get domain info from registry
                    $domainInfo = $this->eppService->getDomainInfo($domain->name);

                    // Log the raw response for debugging
                    Log::debug('Registry response for renewal', [
                        'domain' => $domain->name,
                        'response' => $domainInfo,
                    ]);

                    // Check directly for the expiry date in the flat structure returned by getDomainInfo
                    if (! $domainInfo) {
                        // This case should ideally be caught by getDomainInfo itself throwing an exception
                        throw new Exception('Received empty or invalid response from registry when fetching domain info.');
                    }

                    // The primary check now is whether 'exDate' exists in the response data
                    if (! isset($domainInfo['exDate'])) {
                        Log::error('Registry response missing "exDate"', [
                            'domain' => $domain->name,
                            'response_data' => $domainInfo, // Log the actual data received
                        ]);
                        // Provide a more specific error based on the missing key
                        throw new Exception('Could not retrieve domain expiry date (exDate) from registry response data.');
                    }

                    // Get the exact expiry date directly from the flat structure
                    $rawExpiryDate = $domainInfo['exDate'];

                    // Parse for our database and display (ensure it's not null/empty first)
                    if (empty($rawExpiryDate)) {
                        throw new Exception('Received an empty expiry date from the registry.');
                    }
                    $registryExpiryDate = Carbon::parse($rawExpiryDate);

                    // Update domain with registry data
                    $domain->update([
                        'expires_at' => $registryExpiryDate,
                    ]);

                    // Reload domain
                    $domain->refresh();

                    // Log the domain info
                    Log::info('Retrieved domain info from registry', [
                        'domain' => $domain->name,
                        'registry_expiry_raw' => $rawExpiryDate,
                        'registry_expiry_parsed' => $registryExpiryDate,
                        'db_expiry' => $domain->expires_at,
                    ]);
                } catch (Exception $e) {
                    // Log the specific error from getDomainInfo during renewal attempt
                    Log::error('Failed to get domain info from registry during renewal process', [
                        'domain' => $domain->name,
                        'error_message' => $e->getMessage(),
                        'error_code' => $e->getCode(),
                        // Avoid logging full trace in production if too verbose, but useful for debugging
                        'trace' => $e->getTraceAsString(),
                    ]);

                    // Provide a more user-friendly message, potentially including the original error for support
                    throw new Exception('Failed to retrieve required domain information from the registry before renewal. Please try again later or contact support. (Details: ' . $e->getMessage() . ')');
                }

                // Create EPP frame for domain renewal using the EXACT registry expiry date
                // Convert period to integer to avoid Carbon::rawAddUnit error
                $periodYears = (int) $request->period;
                $frame = $this->eppService->renewDomain(
                    $domain->name,
                    $rawExpiryDate,
                    $periodYears . 'y'
                );

                // Log the renewal request
                Log::info('Sending domain renewal request', [
                    'domain' => $domain->name,
                    'period' => $periodYears . 'y',
                    'registry_expiry' => $rawExpiryDate,
                ]);

                // Send the request to registry
                $client = $this->eppService->getClient();
                $response = $client->request($frame);

                if (! ($response instanceof Response)) {
                    throw new Exception('Invalid response received from registry');
                }

                // Get the result details
                $result = $response->results()[0];
                if (! $result) {
                    throw new Exception('No result in registry response');
                }

                // Log the response
                Log::debug('Registry response received', [
                    'domain' => $domain->name,
                    'code' => $result->code(),
                    'message' => $result->message(),
                    'data' => $response->data(),
                ]);

                // Check if the response indicates success (1000-series codes are success)
                if ($result->code() < 1000 || $result->code() >= 2000) {
                    throw new Exception("Registry error (code: {$result->code()}): {$result->message()}");
                }

                // Get the response data (assuming success code was checked)
                $responseData = $response->data();

                // ADD EXPLICIT NULL/ARRAY CHECK HERE
                if (!is_array($responseData)) {
                    Log::error('EPP renewal response data is not an array or is null', [
                        'domain' => $domain->name,
                        'response_type' => gettype($responseData),
                        'response_raw' => $response->saveXML() ?? 'Could not get raw XML', // Log raw XML if possible
                    ]);
                    throw new Exception('Received unexpected data format from registry after successful renewal.');
                }

                // Now safe to proceed assuming $responseData is an array

                // Check if the expected new expiry date is present in the response
                if (! isset($responseData['exDate'])) {
                    // Log this specific issue
                    Log::warning('EPP renewal successful, but response missing new expiry date (exDate)', [
                        'domain' => $domain->name,
                        'response_data' => $responseData,
                    ]);
                    // Decide how to proceed - maybe use old expiry + period? Or throw error?
                    // For now, let's throw an error as this indicates an unexpected registry response
                    throw new Exception('EPP renewal succeeded, but the registry did not return the new expiry date.');
                }

                // Get the new expiry date string from the response
                $newExpiryDateString = $responseData['exDate'];

                // Try to parse the new expiry date string robustly
                try {
                    // Attempt to parse the date string
                    $newExpiryDate = Carbon::parse($newExpiryDateString);
                } catch (\Throwable $parseError) {
                    // Log the parsing error
                    Log::error('Failed to parse new expiry date received from registry', [
                        'domain' => $domain->name,
                        'raw_date_string' => $newExpiryDateString,
                        'parsing_error' => $parseError->getMessage(),
                    ]);
                    // Throw a new exception indicating the parsing failed
                    throw new Exception('Registry returned an unparseable new expiry date: ' . $newExpiryDateString);
                }

                // Log the successful parsing
                Log::info('Successfully parsed new expiry date from registry', [
                    'domain' => $domain->name,
                    'raw_date_string' => $newExpiryDateString,
                    'parsed_date' => $newExpiryDate->toIso8601String(),
                ]);

                // Update domain locally using the PARSED new expiry date from the registry
                $domain->update([
                    'expires_at' => $newExpiryDate, // Use the date parsed from $responseData['exDate']
                    'status' => DomainStatus::ACTIVE,
                ]);

                // Log the successful update
                Log::info('Domain expiry date updated locally', [
                    'domain' => $domain->name,
                    'new_expiry' => $newExpiryDate,
                ]);

                // Create invoice - wrap in try-catch to isolate any errors
                try {
                    // If Invoice::createInvoice exists and should be called, ensure it's properly type-hinted
                    // and that the date is properly formatted/passed

                    // Example of safe invoice creation (uncomment and adapt if needed):
                    /*
                    // Ensure $newExpiryDate is a Carbon instance before passing to createInvoice
                    if (!($newExpiryDate instanceof \Carbon\Carbon)) {
                        throw new Exception('New expiry date is not a Carbon instance before invoice creation');
                    }
                    
                    // Create the invoice with proper type checking
                    $invoice = Invoice::createInvoice(
                        domain: $domain,
                        expiryDate: $newExpiryDate,
                        period: $periodYears
                    );
                    
                    Log::info('Invoice created successfully', [
                        'domain' => $domain->name,
                        'invoice_id' => $invoice->id,
                        'amount' => $invoice->amount,
                    ]);
                    */

                    // For now, just log that we would create an invoice here
                    Log::info('Invoice creation step reached', [
                        'domain' => $domain->name,
                        'expiry_date_type' => get_class($newExpiryDate),
                        'period_years' => $periodYears,
                    ]);
                } catch (Throwable $invoiceError) {
                    // Log the specific invoice creation error but continue the process
                    Log::error('Failed to create invoice but continuing renewal process', [
                        'domain' => $domain->name,
                        'error' => $invoiceError->getMessage(),
                        'trace' => $invoiceError->getTraceAsString(),
                    ]);
                    // We don't throw here to allow the renewal to complete even if invoice fails
                }

                // Remove domain from cart
                Cart::remove($cartItemId);

                DB::commit();

                return redirect()->route('admin.domains.index')
                    ->with('success', 'Domain renewed successfully, ' . $domain->name . ' renewed for ' . $periodYears . 'y !');
            } catch (Exception $e) {
                DB::rollBack();

                // Check if $newExpiryDate exists and is a Carbon instance before formatting
                $expiryDateForLog = 'N/A';
                if (isset($newExpiryDate) && $newExpiryDate instanceof \Carbon\Carbon) {
                    $expiryDateForLog = $newExpiryDate->format('Y-m-d H:i:s');
                } elseif (isset($newExpiryDate)) {
                    // Log the raw value if it exists but isn't Carbon
                    $expiryDateForLog = (string) $newExpiryDate;
                }

                // Log the failure details
                Log::error('Domain renewal process failed: ' . $e->getMessage(), [
                    'domain' => $domain->name ?? 'N/A', // Use null coalesce for safety
                    'user_id' => auth()->user()->id ?? 'N/A',
                    'requested_period' => $request->period ?? 'N/A',
                    'calculated_new_expiry' => $expiryDateForLog, // Use the safe variable
                    'error_code' => $e->getCode(),
                    'error_trace' => $e->getTraceAsString(), // Shortened for brevity, adjust as needed
                ]);

                return redirect()->back()
                    ->with('error', 'Failed to renew domain ' . $domain->name . ' for ' . $periodYears . 'y. Please try again or contact support. Error: ' . $e->getMessage());
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Domain renewal failed: ' . $e->getMessage(), [
                'domain' => $domain->name,
                'period' => $request->period,
                'user_id' => auth()->user()->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to renew domain. Please try again or contact support. Error: ' . $e->getMessage());
        } catch (Throwable $e) {
            DB::rollBack();

            // Safely get domain name and error message as strings
            $domainNameForLog = 'N/A';
            if (isset($domain) && $domain instanceof \App\Models\Domain && isset($domain->name)) {
                $domainNameForLog = (string) $domain->name;
            }
            $errorMessage = (string) $e->getMessage();
            $traceString = (string) $e->getTraceAsString(); // Limit length if needed

            Log::error('Domain renewal failed with throwable: ' . $errorMessage, [
                'domain' => $domainNameForLog,
                'error' => $errorMessage,
                'trace' => $traceString,
            ]);

            // Rethrow using the safe error message
            throw new Exception('Domain renewal failed: ' . $errorMessage);
        } finally {
            $this->eppService->disconnect();
        }
    }
    public function handleCartPeriodCount()
    {
        // if ($operation === '+') {
        //     $this->cartPeriodCount += 1;
        // } else {
        //     $this->cartPeriodCount -= 1;
        // }
        dd($this->periode);
        Cart::update($this->domainId, ['quantity' => $this->periode]);
    }
}
