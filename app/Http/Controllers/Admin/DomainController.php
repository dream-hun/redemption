<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDomainRequest;
use App\Http\Requests\UpdateNameserversRequest;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Domain;
use App\Models\DomainContact;
use App\Models\Nameserver;
use App\Services\Epp\EppService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class DomainController extends Controller
{
    protected EppService $eppService;

    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    public function index(): View
    {
        abort_if(Gate::denies('domain_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domains = Domain::with(['owner', 'contacts.contact', 'nameservers'])
            ->where('owner_id', auth()->id())->get();

        return view('admin.domains.index', compact('domains'));
    }

    public function edit(Domain $domain): View
    {
        abort_if(Gate::denies('domain_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $countries = Country::pluck('name', 'code');
        $domain->load('owner', 'contacts.contact', 'nameservers');
        
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
        
        return view('admin.domains.edit', compact('domain', 'countries', 'contactsByType'));
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
            if (! $eppInfo) {
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
                array_walk_recursive($eppInfo['nameservers'], function ($ns) use (&$flatNameservers) {
                    if (is_string($ns)) {
                        $flatNameservers[] = $ns;
                    }
                });
                $eppInfo['nameservers'] = $flatNameservers;
                
                // If we have EPP nameservers but no local nameserver records, sync them
                if ($domain->nameservers()->count() === 0 && !empty($flatNameservers)) {
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

            return view('admin.domains.show', compact('domain', 'eppInfo', 'contactsByType'));

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

            return view('admin.domains.show', compact('domain', 'contactsByType'));
        }
    }
    
    /**
     * Sync nameservers from EPP to local database
     */
    private function syncNameserversFromEpp(Domain $domain, array $nameservers): void
    {
        try {
            DB::transaction(function () use ($domain, $nameservers) {
                // Update domain nameservers array
                $domain->update([
                    'nameservers' => $nameservers,
                ]);
                
                // Create nameserver records
                foreach ($nameservers as $hostname) {
                    if (!empty($hostname)) {
                        $domain->nameservers()->create([
                            'hostname' => $hostname,
                            'ipv4_addresses' => [],
                            'ipv6_addresses' => [],
                        ]);
                    }
                }
            });
            
            Log::info('Synchronized nameservers from EPP for domain: ' . $domain->name);
        } catch (Exception $e) {
            Log::error('Failed to sync nameservers from EPP: ' . $e->getMessage(), [
                'domain' => $domain->name,
                'nameservers' => $nameservers,
                'exception' => $e,
            ]);
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
                DB::transaction(function () use ($domain) {
                    // Delete related records first
                    $domain->nameservers()->delete();
                    $domain->contacts()->delete();
                    $domain->delete();
                });

                if (request()->wantsJson()) {
                    return response()->json(['message' => 'Domain deleted successfully']);
                }

                return redirect()->route('admin.domains.index')->with('message', 'Domain deleted successfully');
            } else {
                $errorMessage = 'Failed to delete domain: '.$response->message();
                Log::error('EPP domain deletion failed: '.$response->message());

                if (request()->wantsJson()) {
                    return response()->json(['message' => $errorMessage], Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                return back()->with('error', $errorMessage);
            }
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
                DB::transaction(function () use ($domain, $nameservers) {
                    // Delete existing nameservers
                    $domain->nameservers()->delete();
                    
                    // Create new nameservers
                    foreach ($nameservers as $hostname) {
                        if (!empty($hostname)) {
                            $domain->nameservers()->create([
                                'hostname' => $hostname,
                                'ipv4_addresses' => [],
                                'ipv6_addresses' => [],
                            ]);
                        }
                    }
                    
                    // Update domain nameservers array
                    $domain->update([
                        'nameservers' => $nameservers,
                    ]);
                });
                
                return redirect()->route('admin.domains.edit', $domain)
                    ->with('message', 'Nameservers updated successfully');
            } else {
                $errorMessage = 'Failed to update nameservers: ' . $response->message();
                Log::error('EPP nameserver update failed: ' . $response->message());
                
                return back()->with('error', $errorMessage)
                    ->withInput();
            }
        } catch (Exception $e) {
            $errorMessage = 'An error occurred while updating nameservers';
            Log::error('Nameserver update error: ' . $e->getMessage(), [
                'exception' => $e,
                'domain' => $domain->name,
            ]);
            
            return back()->with('error', $errorMessage)
                ->withInput();
        }
    }
    
    /**
     * Update domain contacts
     */
    public function updateContacts(UpdateDomainRequest $request, Domain $domain): RedirectResponse
    {
        abort_if(Gate::denies('domain_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        try {
            $validated = $request->validated();
            $contactTypes = ['registrant', 'admin', 'tech', 'billing'];
            $contactData = [];
            
            foreach ($contactTypes as $type) {
                if (isset($validated[$type . '_contact_id']) && !empty($validated[$type . '_contact_id'])) {
                    $contactData[$type] = $validated[$type . '_contact_id'];
                }
            }
            
            // Update contacts via EPP
            $frame = $this->eppService->updateDomainContacts($domain->name, $contactData);
            $response = $this->eppService->getClient()->request($frame);
            
            if ($response->code() === 1000) {
                // Successfully updated in registry, now update in database
                DB::transaction(function () use ($domain, $contactData) {
                    // Update domain contacts
                    foreach ($contactData as $type => $contactId) {
                        $contact = Contact::where('contact_id', $contactId)->first();
                        
                        if ($contact) {
                            // Check if this contact is already associated with this domain
                            $existingContact = $domain->contacts()
                                ->where('type', $type)
                                ->first();
                                
                            if ($existingContact) {
                                // Update existing association
                                $existingContact->update([
                                    'contact_id' => $contact->id,
                                    'user_id' => auth()->id(),
                                ]);
                            } else {
                                // Create new association
                                $domain->contacts()->create([
                                    'contact_id' => $contact->id,
                                    'type' => $type,
                                    'user_id' => auth()->id(),
                                ]);
                            }
                            
                            // Update the domain's contact reference
                            $domain->update([
                                $type . '_contact_id' => $contactId,
                            ]);
                        }
                    }
                });
                
                return redirect()->route('admin.domains.edit', $domain)
                    ->with('message', 'Domain contacts updated successfully');
            } else {
                $errorMessage = 'Failed to update domain contacts: ' . $response->message();
                Log::error('EPP domain contact update failed: ' . $response->message());
                
                return back()->with('error', $errorMessage)
                    ->withInput();
            }
        } catch (Exception $e) {
            $errorMessage = 'An error occurred while updating domain contacts';
            Log::error('Domain contact update error: ' . $e->getMessage(), [
                'exception' => $e,
                'domain' => $domain->name,
            ]);
            
            return back()->with('error', $errorMessage)
                ->withInput();
        }
    }
}
