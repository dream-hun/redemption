<?php

declare(strict_types=1);

namespace App\Services;

use AfriCC\EPP\Client as EPPClient;
use AfriCC\EPP\Frame\Command\Create\Contact as ContactCreate;
use AfriCC\EPP\Frame\Command\Info\Domain as DomainInfo;
use AfriCC\EPP\Frame\Command\Info\Domain as InfoDomain;
use AfriCC\EPP\Frame\Command\Update\Domain as DomainUpdate;
use AfriCC\EPP\Frame\Response;
use Exception;
use Illuminate\Support\Facades\Log;

final class EppService
{
    private EPPClient $client;

    private array $config;

    private bool $connected = false;

    public function __construct()
    {
        $this->config = [
            'host' => config('epp.host'),
            'username' => config('epp.username'),
            'password' => config('epp.password'),
            'services' => config('epp.services'),
            'debug' => config('epp.debug'),
        ];
        $this->client = new EPPClient($this->config);
    }

    public function getClient(): EPPClient
    {
        return $this->client;
    }

    public function ensureConnection(): void
    {
        if (! $this->connected) {
            $this->client->connect();
            $this->connected = true;
        }
    }

    public function disconnect(): void
    {
        if ($this->connected) {
            $this->client->close();
            $this->connected = false;
        }
    }

    /**
     * Verify domain auth_code
     */
    public function verifyAuthCode(string $domain, string $authCode): array
    {
        try {
            $this->ensureConnection();

            $frame = new DomainInfo();
            $frame->setDomain($domain);
            $frame->setAuthInfo($authCode);
            $response = $this->client->request($frame);

            Log::debug('Auth code verification response', [
                'domain' => $domain,
                'response' => $response->data(),
            ]);

            return [
                'success' => $response->code() === 1000,
                'message' => $response->message(),
                'code' => $response->code(),
            ];
        } catch (Exception $e) {
            Log::error('Auth code verification failed', [
                'domain' => $domain,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Create new registrant contact
     * @throws Exception
     */
    public function createContact(array $contactData): array
    {
        try {
            $this->ensureConnection();

            $frame = new ContactCreate();
            $frame->setId($contactData['contact_id']);
            $frame->setName($contactData['name']);
            if (! empty($contactData['organization'])) {
                $frame->setOrganization($contactData['organization']);
            }
            $frame->setCity($contactData['city']);
            $frame->setCountryCode($contactData['country_code']);
            $frame->setEmail($contactData['email']);

            $response = $this->client->request($frame);

            Log::debug('Contact creation response', [
                'contact_id' => $contactData['contact_id'],
                'response' => $response->data(),
            ]);

            return [
                'success' => $response->code() === 1000,
                'message' => $response->message(),
                'code' => $response->code(),
            ];
        } catch (Exception $e) {
            Log::error('Contact creation failed', [
                'contact_id' => $contactData['contact_id'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Update domain registrant
     * @throws Exception
     */
    public function updateRegistrant(string $domain, string $newRegistrantId, string $authCode): array
    {
        try {
            $this->ensureConnection();

            $frame = new DomainUpdate();
            $frame->setDomain($domain);
            $frame->changeRegistrant($newRegistrantId);
            $frame->changeAuthInfo($authCode);

            $response = $this->client->request($frame);

            Log::debug('Domain registrant update response', [
                'domain' => $domain,
                'new_registrant_id' => $newRegistrantId,
                'response' => $response->data(),
            ]);

            return [
                'success' => $response->code() === 1000,
                'message' => $response->message(),
                'code' => $response->code(),
            ];
        } catch (Exception $e) {
            Log::error('Domain registrant update failed', [
                'domain' => $domain,
                'new_registrant_id' => $newRegistrantId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Get auth_code for a domain
     */
    public function getAuthCode(string $domain): array
    {
        try {
            $this->ensureConnection();

            $frame = new DomainInfo();
            $frame->setDomain($domain);
            $response = $this->client->request($frame);

            if ($response->code() === 1000) {
                $data = $response->data();
                $authCode = $data['infData']['authInfo']['pw'] ?? null;
                if ($authCode) {
                    Log::debug('Auth code retrieved', [
                        'domain' => $domain,
                        'auth_code' => $authCode,
                    ]);

                    return [
                        'success' => true,
                        'auth_code' => $authCode,
                    ];
                }
                Log::warning('Auth code not found in response', [
                    'domain' => $domain,
                    'response' => $data,
                ]);

                return [
                    'success' => false,
                    'message' => 'Auth code not found.',
                    'code' => 404,
                ];
            }

            Log::error('Failed to retrieve auth code', [
                'domain' => $domain,
                'response' => $response->data(),
            ]);

            return [
                'success' => false,
                'message' => $response->message(),
                'code' => $response->code(),
            ];
        } catch (Exception $e) {
            Log::error('Auth code retrieval failed', [
                'domain' => $domain,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Get domain info from registry
     *
     * @throws Exception
     */
    public function getDomainInfo(string $domain): array
    {
        try {
            $this->ensureConnection();

            // Create and send request
            $frame = new InfoDomain;
            $frame->setDomain($domain);

            // Log request for debugging
            Log::debug('Sending domain info request', ['domain' => $domain]);

            $response = $this->client->request($frame);

            // Validate response
            if (! ($response instanceof Response) || ! ($result = $response->results()[0])) {
                throw new Exception('Invalid response from registry');
            }

            // Check response status
            if ($result->code() < 1000 || $result->code() >= 2000) {
                Log::error('Registry error in getDomainInfo', [
                    'domain' => $domain,
                    'code' => $result->code(),
                    'message' => $result->message(),
                ]);
                throw new Exception("Registry error (code: {$result->code()}): {$result->message()}");
            }

            // Get response data
            $data = $response->data();

            // Check if data is nested in 'infData' key (common EPP response format)
            if (isset($data['infData']) && is_array($data['infData'])) {
                // Extract data from nested structure
                $infData = $data['infData'];

                // Log the structure for debugging
                Log::debug('EPP response has nested infData structure', [
                    'domain' => $domain,
                    'infData' => $infData,
                ]);

                // Merge infData with main data array, giving priority to infData values
                $data = array_merge($data, $infData);
            }

            // Use queried domain if name not in response
            if (empty($data['name'])) {
                $data['name'] = $domain;
                Log::warning('Domain name not found in EPP response, using queried name', [
                    'domain' => $domain,
                    'response_data' => $data,
                ]);
            }

            // Extract nameservers from nested structure if present
            $nameservers = [];
            if (! empty($data['ns']['hostObj']) && is_array($data['ns']['hostObj'])) {
                $nameservers = $data['ns']['hostObj'];
            } elseif (! empty($data['ns']) && is_array($data['ns'])) {
                $nameservers = $data['ns'];
            }

            // Extract contacts from nested structure
            $adminContacts = $data['contact@admin'] ?? [];
            $techContacts = $data['contact@tech'] ?? [];
            $billingContacts = $data['contact@billing'] ?? [];

            // Ensure contacts are in array format
            if (! is_array($adminContacts)) {
                $adminContacts = [$adminContacts];
            }
            if (! is_array($techContacts)) {
                $techContacts = [$techContacts];
            }
            if (! is_array($billingContacts)) {
                $billingContacts = [$billingContacts];
            }

            // Format and return domain info
            return [
                'name' => $data['name'],
                'roid' => $data['roid'] ?? null,
                'status' => is_array($data['status'] ?? null) ? $data['status'] : [$data['status'] ?? null],
                'registrant' => $data['registrant'] ?? null,
                'contacts' => [
                    'admin' => $adminContacts === [] ? $data['admin'] ?? null : ($adminContacts),
                    'tech' => $techContacts === [] ? $data['tech'] ?? null : ($techContacts),
                    'billing' => $billingContacts === [] ? $data['billing'] ?? null : ($billingContacts),
                ],
                'nameservers' => $nameservers,
                'hosts' => is_array($data['host'] ?? null) ? $data['host'] : [],
                'clID' => $data['clID'] ?? null,
                'crID' => $data['crID'] ?? null,
                'crDate' => $data['crDate'] ?? null,
                'upID' => $data['upID'] ?? null,
                'upDate' => $data['upDate'] ?? null,
                'exDate' => $data['exDate'] ?? null,
                'trDate' => $data['trDate'] ?? null,
                'authInfo' => $data['authInfo'] ?? null,
            ];
        } catch (Exception $e) {
            Log::error('Failed to get domain info: '.$e->getMessage(), [
                'domain' => $domain,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
