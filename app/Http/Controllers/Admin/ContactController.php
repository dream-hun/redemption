<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateContactRequest;
use App\Http\Requests\Admin\UpdateContactRequest;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Domain;
use App\Services\Epp\EppService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends Controller
{
    protected EppService $eppService;

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
            $data = $request->validated();
            $contactIds = Contact::generateContactIds();
            $createdContacts = [];

            // Check if type exists in the data, default to 'all' if not present
            $contactType = $data['type'] ?? 'all';

            // Determine which contact types to create
            $contactTypes = $contactType === 'all'
                ? ['admin', 'billing', 'registrant', 'tech']
                : [$contactType];

            foreach ($contactTypes as $type) {
                try {
                    // Get the contact ID for this type
                    $contactId = $contactIds[$type] ?? Contact::generateContactIds($type);

                    // Prepare contact data for EPP service
                    $contactData = [
                        'id' => $contactId,
                        'name' => $data['name'],
                        'organization' => $data['organization'] ?? '',
                        'streets' => array_filter([
                            $data['street1'],
                            $data['street2'] ?? null,
                        ]),
                        'city' => $data['city'],
                        'province' => $data['province'],
                        'postal_code' => $data['postal_code'],
                        'country_code' => $data['country_code'],
                        'voice' => $data['voice'],
                        'fax' => [
                            'number' => $data['fax'] ?? '',
                            'ext' => $data['fax_ext'] ?? '',
                        ],
                        'email' => $data['email'],
                        'disclose' => ['voice', 'email'], // Default disclosure settings
                    ];

                    // Create EPP contact
                    $eppResult = $this->eppService->createContacts($contactData);

                    // Store in database
                    $contact = Contact::create([
                        'contact_id' => $contactId,
                        'contact_type' => $type,
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
                        'auth_info' => $eppResult['auth'],
                        'disclose' => ['voice', 'email'],
                        'type' => $type,
                        'user_id' => auth()->user()->id,
                    ]);

                    $createdContacts[$type] = $contact;

                } catch (Exception $e) {
                    // If any contact creation fails, delete all previously created contacts
                    foreach ($createdContacts as $createdContact) {
                        $createdContact->delete();
                    }

                    throw new Exception("Failed to create $type contact: ".$e->getMessage());
                }
            }

            return redirect()->route('admin.contacts.index')->with('session', 'Contacts created successfully');

        } catch (Exception $e) {
            Log::error('Failed to create contacts: '.$e->getMessage(), [
                'timestamp' => now(),
                'data' => $request->validated(),
            ]);

            return response()->json([
                'message' => 'Failed to create contacts',
                'error' => $e->getMessage(),
            ], 500);
        } finally {
            $this->eppService->disconnect();
        }
    }

    /**
     * Show the form for editing a contact
     *
     * @param Contact $contact
     * @return View
     */
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
     * @param UpdateContactRequest $request
     * @param Contact $contact
     * @return RedirectResponse
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        try {
            $data = $request->validated();
            
            // Always assume the contact exists in the EPP registry if it has a contact_id
            // This prevents "Object exists" errors when trying to create a contact that already exists
            $contactExists = !empty($contact->contact_id);
            
            Log::info('Contact update operation', [
                'contact_id' => $contact->contact_id,
                'assuming_exists' => $contactExists,
            ]);
            
            // Prepare contact data for EPP service
            $contactData = [
                'id' => $contact->contact_id,
                'name' => $data['name'],
                'organization' => $data['organization'] ?? '',
                'streets' => array_filter([
                    $data['street1'],
                    $data['street2'] ?? null,
                ]),
                'city' => $data['city'],
                'province' => $data['province'],
                'postal_code' => $data['postal_code'],
                'country_code' => $data['country_code'],
                'voice' => $data['voice'],
                'fax' => [
                    'number' => $data['fax'] ?? '',
                    'ext' => $data['fax_ext'] ?? '',
                ],
                'email' => $data['email'],
                'disclose' => ['voice', 'email'], // Default disclosure settings
            ];
            
            $eppResult = null;
            $newAuthInfo = null;
            
            // Update or create contact in EPP registry
            if ($contactExists) {
                Log::info('Updating existing contact in EPP registry', ['contact_id' => $contact->contact_id]);
                
                // Update existing contact
                $updateResult = $this->eppService->updateContact($contact->contact_id, $contactData);
                $eppResult = $updateResult['frame'];
                $newAuthInfo = $updateResult['auth'];
                
                // Send update request to EPP registry
                $client = $this->eppService->getClient();
                $response = $client->request($eppResult);
                
                // Check response status
                $result = $response->results()[0];
                if ($result->code() < 1000 || $result->code() >= 2000) {
                    throw new Exception("EPP registry error (code: {$result->code()}): {$result->message()}");
                }
                
                Log::info('Contact updated successfully in EPP registry', [
                    'contact_id' => $contact->contact_id,
                    'response_code' => $result->code(),
                ]);
                
            } else {
                Log::info('Creating new contact in EPP registry', ['contact_id' => $contact->contact_id]);
                
                // Create new contact
                $createResult = $this->eppService->createContacts($contactData);
                $eppResult = $createResult['frame'];
                $newAuthInfo = $createResult['auth'];
                
                // Send create request to EPP registry
                $client = $this->eppService->getClient();
                $response = $client->request($eppResult);
                
                // Check response status
                $result = $response->results()[0];
                if ($result->code() < 1000 || $result->code() >= 2000) {
                    throw new Exception("EPP registry error (code: {$result->code()}): {$result->message()}");
                }
                
                Log::info('Contact created successfully in EPP registry', [
                    'contact_id' => $contact->contact_id,
                    'response_code' => $result->code(),
                ]);
            }
            
            // Update contact in database
            $contact->update([
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
                'contact_type' => $data['type'] ?? $contact->contact_type, // Update contact type if provided
                'auth_info' => $newAuthInfo ?? $contact->auth_info, // Update auth_info if a new one was generated
            ]);
            
            return redirect()->route('admin.contacts.index')->with('success', 'Contact updated successfully');
            
        } catch (Exception $e) {
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
}
