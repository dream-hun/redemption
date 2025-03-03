<?php

namespace App\Http\Controllers;

use App\Models\DomainPricing;
use App\Services\Epp\EppService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Log;

class DomainSearchController extends Controller
{
    private EppService $eppService;

    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    public function index(): View
    {
        $tlds = DomainPricing::where('status', 'active')
            ->select('tld', 'register_price')
            ->get();

        return view('domains.index', compact('tlds'));
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $domain = $request->input('domains');
            Log::debug('Domain search request:', ['domain' => $domain]);

            if (empty($domain)) {
                return response()->json([
                    'error' => 'No domain provided.',
                ], 400);
            }

            // Get all active TLDs with their pricing
            $tlds = DomainPricing::where('status', 'active')->get();
            Log::debug('Found TLDs:', ['count' => $tlds->count(), 'tlds' => $tlds->pluck('tld')->toArray()]);

            if ($tlds->isEmpty()) {
                return response()->json([
                    'error' => 'No TLDs configured in the system.',
                ], 400);
            }

            $results = [];
            foreach ($tlds as $tld) {
                $domainWithTld = $domain.'.'.ltrim($tld->tld, '.');
                Log::debug('Checking domain:', ['domain' => $domainWithTld]);

                // Check domain availability with EPP
                $eppResults = $this->eppService->checkDomain([$domainWithTld]);
                Log::debug('EPP results:', ['results' => $eppResults]);

                if (! empty($eppResults) && isset($eppResults[$domainWithTld])) {
                    $result = $eppResults[$domainWithTld];
                    $results[$domainWithTld] = (object) [
                        'name' => $domainWithTld,
                        'available' => $result->available,
                        'reason' => $result->reason,
                        'tld' => $tld->tld,
                        'register_price' => $tld->register_price,
                        'transfer_price' => $tld->transfer_price,
                        'renew_price' => $tld->renew_price,
                        'grace' => $tld->grace,
                        'redemption_price' => $tld->redemption_price,
                    ];
                }
            }

            Log::debug('Final results:', ['count' => count($results), 'results' => $results]);

            // Return empty object if no results
            if (empty($results)) {
                return response()->json([
                    'results' => (object) [],
                ]);
            }

            return response()->json([
                'results' => (object) $results,
            ]);

        } catch (Exception $e) {
            Log::error('Domain check error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'An error occurred while searching for domains.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
