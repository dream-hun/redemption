<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Domain;
use App\Services\Epp\EppService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DomainController extends Controller
{
    protected EppService $eppService;

    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    public function index()
    {
        abort_if(Gate::denies('domain_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domains = Domain::with(['registrantContact', 'adminContact', 'techContact', 'owner'])
            ->where('owner_id',auth()->id())->get();

        return view('admin.domains.index', compact('domains'));
    }

    public function edit(Domain $domain)
    {
        abort_if(Gate::denies('domain_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $countries = Country::pluck('name', 'code');
        $domain->load('registrantContact', 'adminContact', 'techContact', 'owner', 'billingContact');
        $contacts = [
            'registrant' => $domain->registrantContact,
            'admin' => $domain->adminContact,
            'tech' => $domain->techContact,
            'billing' => $domain->billingContact,
        ];

        return view('admin.domains.edit', compact('domain', 'countries', 'contacts'));
    }

    public function show(Domain $domain)
    {
        abort_if(Gate::denies('domain_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domain->load('registrantContact', 'adminContact', 'techContact', 'billingContact', 'owner');

        try {
            // Ensure we have a valid domain with name
            if (!$domain->name) {
                throw new Exception('Domain name not found in local database');
            }

            // Attempt to fetch EPP info
            $eppInfo = $this->eppService->getDomainInfo($domain->name);
            if (!$eppInfo) {
                throw new Exception('No EPP information returned for domain');
            }

            // Format dates for display
            $datesToFormat = ['crDate', 'upDate', 'exDate', 'trDate'];
            foreach ($datesToFormat as $dateField) {
                if (!empty($eppInfo[$dateField])) {
                    $eppInfo[$dateField] = date('Y-m-d H:i:s', strtotime($eppInfo[$dateField]));
                }
            }

            // Process nameservers for display
            if (!empty($eppInfo['nameservers']) && is_array($eppInfo['nameservers'])) {
                // Ensure nameservers are in a flat array format for the view
                $flatNameservers = [];
                array_walk_recursive($eppInfo['nameservers'], function($ns) use (&$flatNameservers) {
                    if (is_string($ns)) {
                        $flatNameservers[] = $ns;
                    }
                });
                $eppInfo['nameservers'] = $flatNameservers;
            }

            // Process contacts for display
            if (!empty($eppInfo['contacts'])) {
                foreach (['admin', 'tech', 'billing'] as $contactType) {
                    if (isset($eppInfo['contacts'][$contactType]) && !is_array($eppInfo['contacts'][$contactType])) {
                        $eppInfo['contacts'][$contactType] = [$eppInfo['contacts'][$contactType]];
                    }
                }
            }

            return view('admin.domains.show', compact('domain', 'eppInfo'));

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
            return view('admin.domains.show', compact('domain'));
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
                $domain->delete();

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
            Log::error('Domain deletion error: '.$e->getMessage());

            if (request()->wantsJson()) {
                return response()->json(['message' => $errorMessage], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return back()->with('error', $errorMessage);
        }
    }
}
