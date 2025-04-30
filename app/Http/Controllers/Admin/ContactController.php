<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateContactRequest;
use App\Http\Requests\Admin\UpdateContactRequest;
use App\Models\Contact;
use App\Models\Country;
use App\Services\Epp\EppService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ContactController extends Controller
{
    private EppService $eppService;

    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    public function index()
    {
        abort_if(Gate::denies('contact_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $contacts = Contact::where('user_id', '=', auth()->id())->get();

        return view('admin.contacts.index', ['contacts' => $contacts]);
    }

    public function create()
    {
        abort_if(Gate::denies('contact_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $countries = Country::all();

        return view('admin.contacts.create', ['countries' => $countries]);
    }

    public function store(CreateContactRequest $request)
    { 
        try {
            DB::beginTransaction();

            $data = $request->validated();
          //  dd($data);
            $contactId = Contact::generateContactIds();

            // First check if contact exists in EPP registry
            $checkResult = $this->eppService->checkContacts([$contactId]);
          //  dd($checkResult);
            if ($checkResult === [] || ! isset($checkResult[$contactId])) {
                throw new Exception('Failed to check contact availability in registry');
            }

            if (! $checkResult[$contactId]->available) {
                throw new Exception('Contact ID already exists in registry: ' . ($checkResult[$contactId]->reason ?? 'Unknown reason'));
            }

            Log::info('Contact ID available in EPP registry', ['contact_id' => $contactId]);

            // Format phone numbers according to EPP format (+CC.number)
            $voice = $data['voice'];
            if (! str_starts_with($voice, '+')) {
                $voice = '+250.' . mb_ltrim($voice, '0'); // Using Rwanda country code
            }

            // $fax = empty($data['fax']) ? '' : $data['fax'];
            // if ($fax && ! str_starts_with($fax, '+')) {
            //     $fax = '+250.' . mb_ltrim($fax, '0');
            // }

            // Prepare contact data for EPP service - following EPP protocol format
            $contactData = [
                'contact_id' => $contactId,
                'name' => $data['name'],
                'organization' => $data['organization'] ?? '',
                'street1' => $data['street1'],
                'street2' => $data['street2'] ?? '',
                'city' => $data['city'],
                'province' => $data['province'],
                'postal_code' => $data['postal_code'],
                'country_code' => $data['country_code'],
                'voice' => $voice,
                'fax' => $voice,
                'fax_ext' => '',
                'email' => $data['email'],
                'auth_info' => Str::random(16), 
                'disclose' => ['voice', 'email'],
            ];

            Log::debug('Contact data:', ['data' => $data]);
            Log::debug('Prepared EPP data:', ['data' => $contactData]);

            Log::debug('Prepared contact data for EPP:', ['data' => $contactData]);
           // dd($contactData);
            // Create EPP contact
            $eppResult = $this->eppService->createContacts($contactData);
            // dd($eppResult);
            if ($eppResult === [] || ! is_array($eppResult) || $eppResult === []) {
                throw new Exception('Failed to create contact in registry: ' . ($eppResult['message'] ?? 'Unknown error'));
            }

            // Get the first result since we're only creating one contact
            $contactResult = is_array($eppResult) ? reset($eppResult) : null;
           // dd($contactResult);
            // if (! $contactResult || ! isset($contactResult['contact_id'])) {
            //     throw new Exception('Invalid contact creation response from registry');
            // }

            // Add a small delay before verification to allow for EPP propagation
            usleep(500000); // 500ms delay

            // Verify contact was created in EPP registry
            $eppContact = $this->eppService->infoContact($contactId);

            if ($eppContact === null || $eppContact === []) {
                throw new Exception('Failed to get contact info from registry');
            }

            if (! isset($eppContact['contact'])) {
                Log::error('Invalid contact info response', ['response' => $eppContact]);
                throw new Exception('Invalid contact info response from registry');
            }

            Log::info('Contact verified in EPP registry', ['contact_id' => $contactId]);

            Log::info('Contact created in EPP registry', [
                'contact_id' => $contactId,
                'epp_data' => $eppContact,
            ]);

            // Store in database with the same contact_id as EPP registry
            $contact = Contact::create([
                'contact_id' => $contactId,
                'name' => $data['name'],
                'organization' => $data['organization'] ?? null,
                'street1' => $data['street1'],
                'street2' => $data['street2'] ?? null,
                'city' => $data['city'],
                'province' => $data['province'],
                'postal_code' => $data['postal_code'],
                'country_code' => $data['country_code'],
                'voice' => $data['voice'],
                'fax_number' => $data['fax'] ?? null,
                'fax_ext' => $data['fax_ext'] ?? null,
                'email' => $data['email'],
                'auth_info' => $eppResult['auth'] ?? null,
                'disclose' => ['voice', 'email'],
                'epp_status' => 'active',
                'user_id' => auth()->id(),
            ]);

            Log::info('Contact created in database', [
                'contact_id' => $contactId,
                'contact' => $contact->toArray(),
            ]);

            DB::commit();

            return redirect()
                ->route('admin.contacts.index')
                ->with('success', 'Contact created successfully');
        } catch (Exception | Throwable $e) {
            DB::rollBack();

            Log::error('Failed to create contact', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create contact: ' . $e->getMessage());
        } finally {
            $this->eppService->disconnect();
        }
    }

    public function edit(Contact $contact): View
    {
        abort_if(Gate::denies('contact_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $countries = Country::all();

        return view('admin.contacts.edit', [
            'contact' => $contact,
            'countries' => $countries->pluck('name', 'code')->toArray(),
        ]);
    }

    /**
     * Update contact information in both database and EPP registry
     *
     * @return RedirectResponse
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            if (empty($contact->contact_id)) {
                throw new Exception('Contact ID is required for update');
            }

            // First verify contact exists in EPP registry
            $eppInfo = $this->eppService->infoContact($contact->contact_id);
            if ($eppInfo === null || $eppInfo === [] || ! isset($eppInfo['contact'])) {
                throw new Exception('Contact not found in EPP registry');
            }

            // Format phone numbers according to EPP format (+CC.number)
            $voice = $data['voice'];
            if (! str_starts_with($voice, '+')) {
                $voice = '+250.' . mb_ltrim($voice, '0'); // Using Rwanda country code
            }

            $fax = empty($data['fax']) ? '' : $data['fax'];
            if ($fax && ! str_starts_with($fax, '+')) {
                $fax = '+250.' . mb_ltrim($fax, '0');
            }

            // Prepare contact data for EPP service - following EPP protocol format
            $contactData = [
                'contact_id' => $contact->contact_id,
                'name' => $data['name'],
                'organization' => $data['organization'] ?? '',
                'streets' => array_filter([
                    $data['street1'],
                    $data['street2'] ?? null,
                ]),
                'city' => $data['city'],
                'province' => $data['province'] ?? '',
                'postal_code' => $data['postal_code'] ?? '',
                'country_code' => $data['country_code'],
                'voice' => $voice,
                'fax' => $fax,
                'fax_ext' => $data['fax_ext'] ?? '',
                'email' => $data['email'],
                'disclose' => ['voice', 'email'],
            ];

            Log::info('Updating contact in EPP registry', [
                'contact_id' => $contact->contact_id,
                'data' => $contactData,
            ]);

            try {
                // Update contact in EPP registry
                $updateResult = $this->eppService->updateContact($contact->contact_id, $contactData);
                if ($updateResult === [] || ! isset($updateResult['success']) || ! $updateResult['success']) {
                    throw new Exception('Failed to update contact in registry: ' . ($updateResult['message'] ?? 'Unknown error'));
                }

                // Verify the update was successful
                $updatedEppInfo = $this->eppService->infoContact($contact->contact_id);
                if ($updatedEppInfo === null || $updatedEppInfo === [] || ! isset($updatedEppInfo['contact'])) {
                    throw new Exception('Failed to verify contact update in registry');
                }

                Log::info('Contact updated in EPP registry', [
                    'contact_id' => $contact->contact_id,
                    'epp_data' => $updatedEppInfo,
                ]);
            } catch (Exception $e) {
                Log::error('EPP operation failed', [
                    'contact_id' => $contact->contact_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }

            // Update contact in database
            Log::info('Updating contact in database', [
                'contact_id' => $contact->contact_id,
                'data' => $data,
            ]);

            $contact->update([
                'contact_id' => $contact->contact_id,
                'name' => $data['name'],
                'organization' => $data['organization'] ?? null,
                'street1' => $data['street1'],
                'street2' => $data['street2'] ?? null,
                'city' => $data['city'],
                'province' => $data['province'],
                'postal_code' => $data['postal_code'],
                'country_code' => $data['country_code'],
                'voice' => $data['voice'],
                'fax_number' => $data['fax'] ?? null,
                'fax_ext' => $data['fax_ext'] ?? null,
                'email' => $data['email'],
                'epp_status' => 'active',
            ]);

            DB::commit();

            return redirect()->route('admin.contacts.index')->with('success', 'Contact updated successfully');
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to update contact: ' . $e->getMessage(), [
                'contact_id' => $contact->contact_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.contacts.index')->with('error', 'Failed to update contact: ' . $e->getMessage());
        } finally {
            $this->eppService->disconnect();
        }
    }

    public function destroy(Contact $contact)
    {
        abort_if(Gate::denies('contact_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            DB::beginTransaction();

            // First verify contact exists in EPP registry
            $eppInfo = $this->eppService->infoContact($contact->contact_id);
            if ($eppInfo === null || $eppInfo === [] || ! isset($eppInfo['contact'])) {
                Log::warning('Contact not found in EPP registry, proceeding with local deletion', [
                    'contact_id' => $contact->contact_id,
                ]);
            } else {
                // Delete contact from EPP registry
                Log::info('Deleting contact from EPP registry', [
                    'contact_id' => $contact->contact_id,
                ]);

                $deleteResult = $this->eppService->deleteContact($contact->contact_id);
                if ($deleteResult === [] || ! $deleteResult['success']) {
                    throw new Exception('Failed to delete contact from registry: ' . ($deleteResult['message'] ?? 'Unknown error'));
                }

                // Verify deletion
                $verifyInfo = $this->eppService->infoContact($contact->contact_id);
                if ($verifyInfo && isset($verifyInfo['contact'])) {
                    throw new Exception('Contact still exists in registry after deletion');
                }

                Log::info('Contact deleted from EPP registry', [
                    'contact_id' => $contact->contact_id,
                ]);
            }

            // Delete contact from database
            Log::info('Deleting contact from database', [
                'contact_id' => $contact->contact_id,
                'contact' => $contact->toArray(),
            ]);

            $contact->delete();

            DB::commit();

            return redirect()->route('admin.contacts.index')
                ->with('success', 'Contact deleted successfully');
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete contact', [
                'contact_id' => $contact->contact_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.contacts.index')
                ->with('error', 'Failed to delete contact: ' . $e->getMessage());
        } finally {
            $this->eppService->disconnect();
        }
    }
}
