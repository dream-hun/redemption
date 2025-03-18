<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateContactRequest;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Domain;
use App\Services\Epp\EppService;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
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
        $contacts = Contact::where('user_id', '=', auth()->user()->id)->get();

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

    public function edit(Contact $contact, $type, Domain $domain)
    {
        $countries = Country::all();

        return view('admin.contacts.edit', ['contact' => $contact, 'countries' => $countries, 'domain' => $domain, 'type' => $type]);
    }
}
