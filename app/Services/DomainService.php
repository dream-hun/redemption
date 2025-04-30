<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contact;
use App\Models\Domain;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class DomainService
{
    private EppService $eppService;

    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    public function getEppClient(): \App\Services\EppService
    {
        return $this->eppService;
    }

    /**
     * Change registrant for a .rw domain
     */
    public function changeRegistrant(Domain $domain, array $data): array
    {
        try {
            DB::beginTransaction();

            // Verify auth_code
            $authCheck = $this->eppService->verifyAuthCode($domain->name, $data['auth_code']);
            if (! $authCheck['success']) {
                return [
                    'success' => false,
                    'message' => $authCheck['message'],
                    'code' => $authCheck['code'],
                ];
            }

            // Create new registrant contact
            $contactData = [
                'user_id' => auth()->id(),
                'contact_id' => $data['new_registrant_id'],
                'name' => $data['new_registrant_name'],
                'organization' => $data['new_registrant_org'] ?? null,
                'email' => $data['new_registrant_email'],
                'voice' => $data['new_registrant_phone'],
                'street' => $data['new_registrant_address'],
                'city' => $data['new_registrant_city'],
                'country_code' => $data['new_registrant_country'],
            ];

            $contactResult = $this->eppService->createContact($contactData);
            if (! $contactResult['success']) {
                return [
                    'success' => false,
                    'message' => $contactResult['message'],
                    'code' => $contactResult['code'],
                ];
            }

            // Update domain registrant
            $updateResult = $this->eppService->updateRegistrant(
                $domain->name,
                $data['new_registrant_id'],
                $data['auth_code']
            );

            if ($updateResult['success']) {
                // Save new contact to database
                Contact::create($contactData);

                // Update domain registrant_id
                $domain->update(['registrant_id' => $data['new_registrant_id']]);

                DB::commit();

                return [
                    'success' => true,
                    'message' => 'Registrant changed successfully for '.$domain->name,
                    'data' => ['new_registrant_id' => $data['new_registrant_id']],
                ];
            }

            DB::rollBack();

            return [
                'success' => false,
                'message' => $updateResult['message'],
                'code' => $updateResult['code'],
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Domain registrant change failed', [
                'domain' => $domain->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to change registrant: '.$e->getMessage(),
                'code' => 500,
            ];
        } finally {
            $this->eppService->disconnect();
        }
    }

    /**
     * Get auth_code for a domain
     */
    public function getAuthCode(Domain $domain): array
    {
        try {
            return $this->eppService->getAuthCode($domain->name);
        } catch (Exception $e) {
            Log::error('Auth code retrieval failed', [
                'domain' => $domain->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to retrieve auth code: '.$e->getMessage(),
                'code' => 500,
            ];
        }
    }
}
