<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Services\Epp\EppService;
use Closure;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\View\Component;

final class DomainInformationComponent extends Component
{
    private $domain;

    private EppService $eppService;

    /**
     * Create a new component instance.
     *
     * @param  EppService  $eppService  The EPP service instance
     * @param  mixed  $domain  The domain object to display information for
     */
    public function __construct(EppService $eppService, mixed $domain)
    {
        $this->eppService = $eppService;
        $this->domain = $domain;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        try {
            // Ensure we have a valid domain with name
            if (! $this->domain->name) {
                throw new Exception('Domain name not found in local database');
            }

            // Attempt to fetch EPP info
            $eppInfo = $this->eppService->getDomainInfo($this->domain->name);
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
            }

            // Process contacts for display
            if (! empty($eppInfo['contacts'])) {
                foreach (['admin', 'tech', 'billing'] as $contactType) {
                    if (isset($eppInfo['contacts'][$contactType]) && ! is_array($eppInfo['contacts'][$contactType])) {
                        $eppInfo['contacts'][$contactType] = [$eppInfo['contacts'][$contactType]];
                    }
                }
            }

            return view('components.domain-information-component', [
                'domain' => $this->domain,
                'eppInfo' => $eppInfo,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to fetch EPP domain info: '.$e->getMessage(), [
                'domain' => $this->domain->name ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Show the page with local data and appropriate error message
            $errorMessage = $this->domain->name
                ? 'Could not fetch latest domain information from registry. Showing local data only.'
                : 'Domain information is incomplete. Please ensure the domain is properly registered.';

            session()->flash('error', $errorMessage);

            // Return view with local data only
            return view('components.domain-information-component', [
                'domain' => $this->domain,
                'eppInfo' => null,
            ]);
        }
    }
}
