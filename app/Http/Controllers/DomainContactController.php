<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Services\Epp\EppService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DomainContactController extends Controller
{
    private EppService $eppService;

    private const MAX_ATTEMPTS = 5;

    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    /**
     * Generate a unique EPP contact ID
     */
    private function generateUniqueContactId(string $contactType): string
    {
        $attempts = 0;
        $prefix = match ($contactType) {
            'registrant' => 'REG',
            'admin' => 'ADM',
            'tech' => 'TECH',
            default => 'CNT'
        };

        do {
            if ($attempts >= self::MAX_ATTEMPTS) {
                throw new Exception('Failed to generate unique contact ID after multiple attempts');
            }

            // Generate ID: PREFIX-TIMESTAMP-RANDOM
            $contactId = sprintf(
                '%s-%s-%s',
                $prefix,
                now()->format('ymdHis'),
                strtoupper(Str::random(2))
            );

            // Check if ID exists in local database
            $existsInDb = Contact::where('contact_id', $contactId)->exists();

            if ($existsInDb) {
                $attempts++;

                continue;
            }

            // Check if ID is available in EPP
            $checkFrame = $this->eppService->checkContacts([$contactId]);
            $response = $this->eppService->getClient()->request($checkFrame);

            if (! $response || ! $response->success()) {
                throw new Exception('Failed to check contact ID availability with EPP');
            }

            $data = $response->data();

            // Check if the ID is available in the EPP system
            if (! empty($data['chkData']['cd'])) {
                $checkData = $data['chkData']['cd'];
                if (is_array($checkData) && isset($checkData['@id']['avail']) && $checkData['@id']['avail'] === '1') {
                    return $contactId;
                }
            }

            $attempts++;
        } while (true);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'organization' => 'nullable|string',
                'streets' => 'required|array|min:1|max:3',
                'streets.*' => 'required|string',
                'city' => 'required|string',
                'province' => 'required|string',
                'postal_code' => 'required|string',
                'country_code' => 'required|string|size:2',
                'voice' => 'required|string',
                'fax.number' => 'nullable|string',
                'fax.ext' => 'nullable|string',
                'email' => 'required|email',
                'contact_type' => 'required|in:registrant,admin,tech',
            ]);

            return DB::transaction(function () use ($validated) {
                // Generate unique EPP contact ID
                $contactId = $this->generateUniqueContactId($validated['contact_type']);

                Log::info('Generated unique contact ID', ['contact_id' => $contactId]);

                // Prepare contact data for EPP
                $contactData = [
                    'id' => $contactId,
                    'name' => $validated['name'],
                    'organization' => $validated['organization'],
                    'streets' => $validated['streets'],
                    'city' => $validated['city'],
                    'province' => $validated['province'],
                    'postal_code' => $validated['postal_code'],
                    'country_code' => $validated['country_code'],
                    'voice' => $validated['voice'],
                    'fax' => [
                        'number' => $validated['fax']['number'] ?? '',
                        'ext' => $validated['fax']['ext'] ?? '',
                    ],
                    'email' => $validated['email'],
                    'disclose' => [], // Add disclosure settings if needed
                ];

                // Create contact in EPP
                $result = $this->eppService->createContacts($contactData);
                $response = $this->eppService->getClient()->request($result['frame']);

                if (! $response || ! $response->success()) {
                    throw new Exception(
                        'Failed to create contact in EPP: '.
                        ($response ? $response->results()[0]->message() : 'No response from server')
                    );
                }

                // Create contact in database
                $domainContact = Contact::create([
                    'contact_id' => $contactId,
                    'contact_type' => $validated['contact_type'],
                    'name' => $validated['name'],
                    'organization' => $validated['organization'],
                    'streets' => $validated['streets'],
                    'city' => $validated['city'],
                    'province' => $validated['province'],
                    'postal_code' => $validated['postal_code'],
                    'country_code' => $validated['country_code'],
                    'voice' => $validated['voice'],
                    'fax_number' => $validated['fax']['number'] ?? null,
                    'fax_ext' => $validated['fax']['ext'] ?? null,
                    'email' => $validated['email'],
                    'auth_info' => $result['auth'],
                    'epp_status' => 'active',
                ]);

                Log::info('Contact created successfully', [
                    'contact_id' => $contactId,
                    'database_id' => $domainContact->id,
                ]);

                return response()->json([
                    'message' => 'Contact created successfully',
                    'contact_id' => $contactId,
                    'auth_info' => $result['auth'],
                    'database_id' => $domainContact->id,
                ], 201);
            });

        } catch (Exception $e) {
            Log::error('Failed to create contact', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to create contact',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
