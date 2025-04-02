<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contact\CreateContactRequest;
use App\Http\Requests\Contact\UpdateContactRequest;
use App\Models\Contact;
use App\Services\Epp\EppService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ContactController extends Controller
{
    protected $eppService;

    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    /**
     * Display a listing of contacts.
     */
    public function index(): View
    {
        $contacts = Contact::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.contacts.index', compact('contacts'));
    }

    /**
     * Show the form for creating a new contact.
     */
    public function create(): View
    {
        return view('admin.contacts.create');
    }

    /**
     * Store a newly created contact in storage.
     */
    public function store(CreateContactRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create contact in EPP registry
            $eppResponse = $this->eppService->createContact(
                $request->validated(),
                auth()->user()
            );

            if (! $eppResponse->success()) {
                throw new Exception('Failed to create contact in registry: '.$eppResponse->message());
            }

            // Create contact in local database
            $contact = Contact::create([
                'contact_id' => $eppResponse->data()['id'],
                'user_id' => auth()->id(),
                'name' => $request->name,
                'organization' => $request->organization,
                'street1' => $request->street1,
                'street2' => $request->street2,
                'city' => $request->city,
                'province' => $request->province,
                'postal_code' => $request->postal_code,
                'country_code' => $request->country_code,
                'voice' => $request->voice,
                'voice_ext' => $request->voice_ext,
                'fax' => $request->fax,
                'fax_ext' => $request->fax_ext,
                'email' => $request->email,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.contacts.index')
                ->with('success', 'Contact created successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Contact creation failed: '.$e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create contact: '.$e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified contact.
     */
    public function edit(Contact $contact): View
    {
        $this->authorize('update', $contact);

        return view('admin.contacts.edit', compact('contact'));
    }

    /**
     * Update the specified contact in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        try {
            DB::beginTransaction();

            // Update contact in EPP registry
            $eppResponse = $this->eppService->updateContact(
                $contact->contact_id,
                $request->validated(),
                auth()->user()
            );

            if (! $eppResponse->success()) {
                throw new Exception('Failed to update contact in registry: '.$eppResponse->message());
            }

            // Update contact in local database
            $contact->update($request->validated());

            DB::commit();

            return redirect()
                ->route('admin.contacts.index')
                ->with('success', 'Contact updated successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Contact update failed: '.$e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update contact: '.$e->getMessage());
        }
    }

    /**
     * Check contact availability
     */
    public function checkAvailability(Request $request)
    {
        try {
            $request->validate([
                'contact_ids' => 'required|array|min:1',
                'contact_ids.*' => 'required|string|max:255',
            ]);

            $contactIds = $request->input('contact_ids');
            $frame = $this->eppService->checkContacts($contactIds);
            $response = $this->eppService->getClient()->request($frame);

            if ($response && $response->success()) {
                $data = $response->data();
                $results = [];

                if (! empty($data) && isset($data['chkData']['cd'])) {
                    // Ensure we have a consistent array structure even for single items
                    $items = isset($data['chkData']['cd'][0]) ? $data['chkData']['cd'] : [$data['chkData']['cd']];

                    foreach ($items as $cd) {
                        $contactId = $cd['id'];
                        $isAvailable = (bool) $cd['@id']['avail'];
                        $reason = $cd['reason'] ?? null;

                        $results[$contactId] = (object) [
                            'id' => $contactId,
                            'available' => $isAvailable,
                            'reason' => $reason,
                        ];
                    }
                }

                return response()->json(['results' => $results]);
            }

            $result = $response ? $response->results()[0] : null;

            return response()->json([
                'error' => 'Contact check failed',
                'code' => $result ? $result->code() : 'unknown',
                'message' => $result ? $result->message() : 'No response',
            ], 400);

        } catch (Exception $e) {
            Log::error('Contact availability check failed: '.$e->getMessage());

            return response()->json(['error' => 'Failed to check contact availability: '.$e->getMessage()], 500);
        }
    }
}
