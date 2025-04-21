<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDomainRequest;
use App\Http\Requests\UpdateNameserversRequest;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Domain;
use App\Models\Nameserver;
use App\Services\Epp\EppService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

final class DomainController extends Controller
{
    private EppService $eppService;

    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    public function index(): View
    {
        abort_if(Gate::denies('domain_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domains = Domain::with(['owner', 'contacts.contact', 'nameservers'])
            ->where('owner_id', auth()->id())->get();

        return view('admin.domains.index', ['domains' => $domains]);
    }

    public function edit(Domain $domain): View
    {
        abort_if(Gate::denies('domain_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $countries = Country::pluck('name', 'code');
        $domain->load('owner', 'contacts.contact', 'nameservers');

        // Get contacts already attached to this domain and the registrant
        $domainContactIds = $domain->contacts()->pluck('contact_id')->toArray();
        $availableContacts = Contact::where('user_id', auth()->id())
            ->where(function ($query) use ($domainContactIds): void {
                // Include contacts that are either:
                // 1. Already attached to this domain
                // 2. Have been used as registrant in any domain
                $query->whereIn('id', $domainContactIds)
                    ->orWhereHas('domainContacts', function ($q): void {
                        $q->where('type', 'registrant');
                    });
            })
            ->select('id', 'name', 'email', 'contact_id')
            ->get();

        // Organize domain contacts by type
        $contactsByType = ['registrant' => null, 'admin' => null, 'tech' => null, 'billing' => null];

        foreach ($domain->contacts as $domainContact) {
            if (in_array($domainContact->type, array_keys($contactsByType))) {
                $contactsByType[$domainContact->type] = $domainContact->contact;
                if ($contactsByType[$domainContact->type]) {
                    $contactsByType[$domainContact->type]->type = $domainContact->type;
                }
            }
        }

        return view('admin.domains.edit', ['domain' => $domain, 'countries' => $countries, 'contactsByType' => $contactsByType, 'availableContacts' => $availableContacts]);
    }

    public function show(Domain $domain): View
    {
        abort_if(Gate::denies('domain_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Load domain with all relationships
        $domain->load(['contacts.contact', 'nameservers', 'owner']);

        // Organize contacts by type for easier access in the view
        $contactsByType = ['registrant' => null, 'admin' => null, 'tech' => null, 'billing' => null];
        foreach ($domain->contacts as $domainContact) {
            if (in_array($domainContact->type, array_keys($contactsByType))) {
                $contactsByType[$domainContact->type] = $domainContact->contact;
            }
        }

        try {
            // Ensure we have a valid domain with name
            if (! $domain->name) {
                throw new Exception('Domain name not found in local database');
            }

            // Attempt to fetch EPP info
            $eppInfo = $this->eppService->getDomainInfo($domain->name);
            if ($eppInfo === []) {
                throw new Exception('No EPP information returned for domain');
            }

            // Format dates for display
            $datesToFormat = ['crDate', 'upDate', 'exDate', 'trDate'];
            foreach ($datesToFormat as $dateField) {
                if (! empty($eppInfo[$dateField])) {
                    $eppInfo[$dateField] = date('Y-m-d H:i:s', strtotime($eppInfo[$dateField]));
                }
            }

            // Process nameservers for display
            if (! empty($eppInfo['nameservers']) && is_array($eppInfo['nameservers'])) {
                // Ensure nameservers are in a flat array format for the view
                $flatNameservers = [];
                array_walk_recursive($eppInfo['nameservers'], function ($ns) use (&$flatNameservers): void {
                    if (is_string($ns)) {
                        $flatNameservers[] = $ns;
                    }
                });
                $eppInfo['nameservers'] = $flatNameservers;

                // If we have EPP nameservers but no local nameserver records, sync them
                if ($domain->nameservers()->count() === 0 && $flatNameservers !== []) {
                    $this->syncNameserversFromEpp($domain, $flatNameservers);
                }
            }

            // Process contacts for display
            if (! empty($eppInfo['contacts'])) {
                foreach (['admin', 'tech', 'billing'] as $contactType) {
                    if (isset($eppInfo['contacts'][$contactType]) && ! is_array($eppInfo['contacts'][$contactType])) {
                        $eppInfo['contacts'][$contactType] = [$eppInfo['contacts'][$contactType]];
                    }
                }

                // If we have EPP contacts but no local contact records, we could sync them here
                // This would require additional implementation to create Contact records from EPP data
            }

            return view('admin.domains.show', ['domain' => $domain, 'eppInfo' => $eppInfo, 'contactsByType' => $contactsByType]);

        } catch (Exception $e) {
            Log::error('Failed to fetch EPP domain info: '.$e->getMessage(), [
                'domain' => $domain->name ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Show the page with local data and appropriate error message
            $errorMessage = $domain->name
                ? 'Could not fetch latest domain information from registry. Showing local data only.'
                : 'Domain information is incomplete. Please ensure the domain is properly registered.';

            session()->flash('error', $errorMessage);

            return view('admin.domains.show', ['domain' => $domain, 'contactsByType' => $contactsByType]);
        }
    }

    public function destroy(Domain $domain): JsonResponse|RedirectResponse
    {
        abort_if(Gate::denies('domain_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Verify domain ownership
        if ($domain->owner_id !== auth()->id()) {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'You do not have permission to delete this domain'], Response::HTTP_FORBIDDEN);
            }

            return back()->with('error', 'You do not have permission to delete this domain');
        }

        try {
            // Delete domain via EPP
            $frame = $this->eppService->deleteDomain($domain->name);
            $response = $this->eppService->getClient()->request($frame);

            if ($response->code() === 1000) {
                // Successfully deleted from registry, now delete from database
                DB::transaction(function () use ($domain): void {
                    // Delete related records first
                    $domain->nameservers()->delete();
                    $domain->contacts()->delete();
                    $domain->delete();
                });

                if (request()->wantsJson()) {
                    return response()->json(['message' => 'Domain deleted successfully']);
                }

                return redirect()->route('admin.domains.index')->with('message', 'Domain deleted successfully');
            }
            $errorMessage = 'Failed to delete domain: '.$response->message();
            Log::error('EPP domain deletion failed: '.$response->message());
            if (request()->wantsJson()) {
                return response()->json(['message' => $errorMessage], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return back()->with('error', $errorMessage);
        } catch (Exception $e) {
            $errorMessage = 'An error occurred while deleting the domain';
            Log::error('Domain deletion error: '.$e->getMessage(), [
                'exception' => $e,
                'domain' => $domain->name,
            ]);

            if (request()->wantsJson()) {
                return response()->json(['message' => $errorMessage], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Update domain nameservers
     */
    public function updateNameservers(UpdateNameserversRequest $request, Domain $domain): RedirectResponse
    {
        abort_if(Gate::denies('domain_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            $nameservers = $request->validated('nameservers');

            // Update nameservers via EPP
            $frame = $this->eppService->updateDomainNameservers($domain->name, $nameservers);
            $response = $this->eppService->getClient()->request($frame);

            if ($response->code() === 1000) {
                // Successfully updated in registry, now update in database
                DB::transaction(function () use ($domain, $nameservers): void {
                    // Delete existing nameservers
                    $domain->nameservers()->delete();

                    // Create new nameservers
                    foreach ($nameservers as $hostname) {
                        if (! empty($hostname)) {
                            $domain->nameservers()->create([
                                'hostname' => $hostname,
                                'ipv4_addresses' => [],
                                'ipv6_addresses' => [],
                            ]);
                        }
                    }

                    // Mark the domain as updated
                    $domain->touch();
                });

                return redirect()->route('admin.domains.edit', $domain)
                    ->with('message', 'Nameservers updated successfully');
            }
            $errorMessage = 'Failed to update nameservers: '.$response->message();
            Log::error('EPP nameserver update failed: '.$response->message());

            return back()->with('error', $errorMessage)
                ->withInput();
        } catch (Exception $e) {
            $errorMessage = 'An error occurred while updating nameservers';
            Log::error('Nameserver update error: '.$e->getMessage(), [
                'exception' => $e,
                'domain' => $domain->name,
            ]);

            return back()->with('error', $errorMessage)
                ->withInput();
        }
    }

    /**
     * Remove a contact from a domain
     */
    public function removeContact(Request $request, Domain $domain, string $contactType): RedirectResponse
    {
        abort_if(Gate::denies('domain_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Prevent removal of registrant contacts
        if ($contactType === 'registrant') {
            return back()->with('error', 'Registrant contact cannot be removed');
        }

        // Validate contact type
        if (! in_array($contactType, ['admin', 'tech', 'billing'])) {
            return back()->with('error', 'Invalid contact type');
        }

        try {
            // Get the domain contact to remove
            $domainContact = $domain->contacts()->where('type', $contactType)->first();

            if (! $domainContact) {
                return back()->with('error', 'Contact not found');
            }

            // Get the contact details for logging
            $contact = $domainContact->contact;
            if (! $contact) {
                return back()->with('error', 'Contact details not found');
            }

            // Update the EPP registry to remove the contact
            $contactData = [];
            $contactsToRemove = [$contactType];

            $frame = $this->eppService->updateDomainContacts($domain->name, $contactData, $contactsToRemove);
            $response = $this->eppService->getClient()->request($frame);

            if ($response->code() === 1000) {
                // Successfully updated in registry, now update in database
                DB::transaction(function () use ($domain, $contactType): void {
                    // Remove the association in the database
                    $domain->contacts()->where('type', $contactType)->delete();

                    // Update the domain's contact reference to null
                    if ($domain->{$contactType.'_contact_id'}) {
                        $domain->update([
                            $contactType.'_contact_id' => null,
                        ]);
                    }
                });

                Log::info("Removed {$contactType} contact from domain {$domain->name}", [
                    'domain_id' => $domain->id,
                    'contact_id' => $contact->id,
                    'contact_type' => $contactType,
                ]);

                return redirect()->route('admin.domains.edit', $domain)
                    ->with('message', ucfirst($contactType).' contact removed successfully');
            }
            $errorMessage = 'Failed to remove contact from registry: '.$response->message();
            Log::error('EPP contact removal failed: '.$response->message());

            return back()->with('error', $errorMessage);
        } catch (Exception $e) {
            $errorMessage = 'An error occurred while removing the contact';
            Log::error('Contact removal error: '.$e->getMessage(), [
                'exception' => $e,
                'domain' => $domain->name,
                'contact_type' => $contactType,
            ]);

            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Update domain contacts
     */
    public function update(UpdateDomainRequest $request, Domain $domain): RedirectResponse
    {
        abort_if(Gate::denies('domain_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Check if this is a contact update request
        if ($request->input('action') === 'update_contacts') {
            return $this->updateContacts($request, $domain);
        }

        // Handle other domain updates here...
        return back()->with('error', 'Invalid update action');
    }

    public function updateContacts(UpdateDomainRequest $request, Domain $domain): RedirectResponse
    {
        abort_if(Gate::denies('domain_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            $validated = $request->validated();
            $contactTypes = ['registrant', 'admin', 'tech', 'billing'];
            $contactData = [];
            $contactsToRemove = [];
            $updatedContactIds = [];

            // Process contact updates and removals
            foreach ($contactTypes as $type) {
                // Skip processing if it's registrant and trying to remove it
                if ($type === 'registrant' && (! isset($validated[$type.'_contact_id']) || empty($validated[$type.'_contact_id']))) {
                    // Registrant is required, so get the current one if not provided
                    $existingRegistrant = $domain->contacts()->where('type', 'registrant')->first();
                    if ($existingRegistrant && $existingRegistrant->contact) {
                        $contactData[$type] = (string) $existingRegistrant->contact->contact_id;
                        $updatedContactIds[$type] = $existingRegistrant->contact->id;
                    }

                    continue;
                }

                // For other contact types
                if (isset($validated[$type.'_contact_id']) && ! empty($validated[$type.'_contact_id'])) {
                    // Get the contact from database first
                    $contact = Contact::find($validated[$type.'_contact_id']);
                    if ($contact) {
                        // Use the EPP contact_id for the registry
                        $contactData[$type] = $contact->contact_id;
                        // Store the database ID for our local updates
                        $updatedContactIds[$type] = $contact->id;
                    }
                } elseif (isset($validated['remove_'.$type]) && $validated['remove_'.$type] && $type !== 'registrant') {
                    // Removing a contact (except registrant)
                    $contactsToRemove[] = $type;
                }
            }

            // Update contacts via EPP only if we have contacts to update or remove
            if ($contactData !== [] || $contactsToRemove !== []) {
                // Log what we're about to do
                Log::info('Updating domain contacts', [
                    'domain' => $domain->name,
                    'contact_data' => $contactData,
                    'contacts_to_remove' => $contactsToRemove,
                ]);

                // Create the EPP frame and send it to the registry
                $frame = $this->eppService->updateDomainContacts($domain->name, $contactData, $contactsToRemove);
                $response = $this->eppService->getClient()->request($frame);

                if ($response->code() === 1000) {
                    // Successfully updated in registry, now update in database
                    DB::transaction(function () use ($domain, $contactData, $contactsToRemove, $updatedContactIds): void {
                        // Process contacts to remove first
                        foreach ($contactsToRemove as $typeToRemove) {
                            Log::info("Removing {$typeToRemove} contact from database for domain {$domain->name}");

                            // Remove the association in the database
                            $domain->contacts()->where('type', $typeToRemove)->delete();

                            // Update the domain's contact reference to null
                            if ($domain->{$typeToRemove.'_contact_id'}) {
                                $domain->update([
                                    $typeToRemove.'_contact_id' => null,
                                ]);
                            }
                        }

                        // Process contacts to add or update
                        foreach ($contactData as $type => $contactId) {
                            // Skip if we don't have a valid database ID for this contact
                            if (! isset($updatedContactIds[$type])) {
                                Log::warning("No valid database ID found for {$type} contact: {$contactId}");

                                continue;
                            }

                            $dbContactId = $updatedContactIds[$type];
                            Log::info("Updating {$type} contact in database for domain {$domain->name}", [
                                'contact_id' => $contactId,
                                'db_contact_id' => $dbContactId,
                            ]);

                            // Check if this contact is already associated with this domain
                            $existingContact = $domain->contacts()
                                ->where('type', $type)
                                ->first();

                            if ($existingContact) {
                                // If the existing contact is different from the new one, update it
                                if ($existingContact->contact_id !== $dbContactId) {
                                    Log::info("Updating existing {$type} contact association", [
                                        'old_contact_id' => $existingContact->contact_id,
                                        'new_contact_id' => $dbContactId,
                                    ]);

                                    // Update existing association using the contact's database ID
                                    $existingContact->update([
                                        'contact_id' => $dbContactId,
                                        'user_id' => auth()->id(),
                                    ]);
                                }
                            } else {
                                // Create new association using the contact's database ID
                                Log::info("Creating new {$type} contact association");
                                $domain->contacts()->create([
                                    'contact_id' => $dbContactId,
                                    'type' => $type,
                                    'user_id' => auth()->id(),
                                ]);
                            }

                            // Update the domain's contact reference with the EPP contact_id
                            // Explicitly cast to string to ensure validation passes
                            $domain->update([
                                $type.'_contact_id' => (string) $contactId,
                            ]);
                        }
                    });

                    if ($request->wantsJson()) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Domain contacts updated successfully',
                        ]);
                    }

                    return redirect()->route('admin.domains.edit', $domain)
                        ->with('message', 'Domain contacts updated successfully');
                }
                $errorMessage = 'Failed to update domain contacts: '.$response->message();
                Log::error('EPP domain contact update failed: '.$response->message(), [
                    'domain' => $domain->name,
                    'response_code' => $response->code(),
                    'response_message' => $response->message(),
                ]);
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                    ], 400);
                }

                return back()->with('error', $errorMessage)
                    ->withInput();
            }

            // No changes to make
            return redirect()->route('admin.domains.edit', $domain)
                ->with('message', 'No contact changes were made');
        } catch (Exception $e) {
            $errorMessage = 'An error occurred while updating domain contacts';
            Log::error('Domain contact update error: '.$e->getMessage(), [
                'exception' => $e,
                'domain' => $domain->name,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', $errorMessage)
                ->withInput();
        }
    }

    /**
     * Sync nameservers from EPP to local database
     */
    private function syncNameserversFromEpp(Domain $domain, array $nameservers): void
    {
        try {
            DB::transaction(function () use ($domain, $nameservers): void {
                // Update domain nameservers array
                $domain->update([
                    'nameservers' => $nameservers,
                ]);

                // Create nameserver records
                foreach ($nameservers as $hostname) {
                    if (! empty($hostname)) {
                        $domain->nameservers()->create([
                            'hostname' => $hostname,
                            'ipv4_addresses' => [],
                            'ipv6_addresses' => [],
                        ]);
                    }
                }
            });

            Log::info('Synchronized nameservers from EPP for domain: '.$domain->name);
        } catch (Exception $e) {
            Log::error('Failed to sync nameservers from EPP: '.$e->getMessage(), [
                'domain' => $domain->name,
                'nameservers' => $nameservers,
                'exception' => $e,
            ]);
        }
    }
}
