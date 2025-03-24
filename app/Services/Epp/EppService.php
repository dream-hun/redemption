<?php

namespace App\Services\Epp;

use AfriCC\EPP\Client as EPPClient;
use AfriCC\EPP\Frame\Command\Check\Contact as CheckContact;
use AfriCC\EPP\Frame\Command\Check\Domain as CheckDomain;
use AfriCC\EPP\Frame\Command\Check\Host as CheckHost;
use AfriCC\EPP\Frame\Command\Create\Contact as CreateContact;
use AfriCC\EPP\Frame\Command\Create\Domain as CreateDomain;
use AfriCC\EPP\Frame\Command\Create\Host as CreateHost;
use AfriCC\EPP\Frame\Command\Delete\Domain as DeleteDomain;
use AfriCC\EPP\Frame\Command\Info\Domain as InfoDomain;
use AfriCC\EPP\Frame\Command\Poll;
use AfriCC\EPP\Frame\Command\Renew\Domain as RenewDomain;
use AfriCC\EPP\Frame\Command\Transfer\Domain as TransferDomain;
use AfriCC\EPP\Frame\Command\Update\Domain as UpdateDomain;
use AfriCC\EPP\Frame\Response;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;

class EppService
{
    private EPPClient $client;

    private array $config;

    private bool $connected = false;

    private int $maxRetries = 3;

    private int $retryDelay = 1; // seconds

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->config = config('epp');

        if (! $this->config) {
            throw new Exception('EPP configuration not found');
        }

        if (empty($this->config['host'])) {
            throw new Exception('EPP host is not configured. Please set EPP_HOST in your .env file.');
        }

        if (empty($this->config['certificate']) || ! file_exists($this->config['certificate'])) {
            throw new Exception('EPP certificate not found. Please check the certificate path in your configuration.');
        }

        $this->initializeClient();
    }

    /**
     * Initialize the EPP client with retries
     *
     * @throws Exception
     */
    private function initializeClient(): void
    {
        $config = [
            'host' => $this->config['host'],
            'port' => (int) $this->config['port'],
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'ssl' => (bool) $this->config['ssl'],
            'local_cert' => $this->config['certificate'],
            'verify_peer' => false,
            'verify_peer_name' => false,
            'verify_host' => false,
            'debug' => (bool) ($this->config['debug'] ?? false),
            'timeout' => 30, // Add timeout
        ];

        $attempts = 0;
        $lastException = null;

        while ($attempts < $this->maxRetries) {
            try {
                $this->client = new EPPClient($config);

                return;
            } catch (Exception $e) {
                $lastException = $e;
                $attempts++;
                Log::warning("EPP Client initialization attempt $attempts failed: ".$e->getMessage());

                if ($attempts < $this->maxRetries) {
                    sleep($this->retryDelay);
                }
            }
        }

        Log::error('EPP Client initialization failed after '.$this->maxRetries.' attempts');
        throw $lastException;
    }

    /**
     * Connect to the EPP server with retries
     *
     * @throws Exception
     */
    public function connect(): ?string
    {
        if ($this->connected) {
            return null;
        }

        $attempts = 0;
        $lastException = null;

        while ($attempts < $this->maxRetries) {
            try {
                $greeting = $this->client->connect();
                $this->connected = true;

                return $greeting;
            } catch (Exception $e) {
                $lastException = $e;
                $attempts++;
                Log::warning("EPP Connection attempt $attempts failed: ".$e->getMessage());

                if ($attempts < $this->maxRetries) {
                    sleep($this->retryDelay);
                }
            }
        }

        Log::error('EPP Connection failed after '.$this->maxRetries.' attempts');
        throw $lastException;
    }

    /**
     * Check if client is connected and try to reconnect if not
     *
     * @throws Exception
     */
    private function ensureConnection(): void
    {
        if (! isset($this->client)) {
            throw new Exception('EPP client not initialized');
        }

        try {
            if (! $this->connected) {
                $greeting = $this->connect();
                Log::info('EPP connection established', [
                    'greeting' => $greeting,
                    'host' => $this->config['host'],
                ]);
            }

            // Test connection with a simple check domain command
            try {
                $frame = new CheckDomain;
                $frame->addDomain($this->config['host']); // Use host as test domain
                $response = $this->client->request($frame);

                if (! ($response instanceof Response)) {
                    $this->connected = false;
                    throw new Exception('EPP connection test failed - invalid response');
                }

                $result = $response->results()[0];
                if (! $result) {
                    $this->connected = false;
                    throw new Exception('EPP connection test failed - no result');
                }

                Log::debug('EPP connection test successful', [
                    'code' => $result->code(),
                    'message' => $result->message(),
                ]);

            } catch (Exception $e) {
                $this->connected = false;
                throw new Exception('EPP connection test failed: '.$e->getMessage());
            }
        } catch (Exception $e) {
            $this->connected = false;
            Log::error('EPP connection error: '.$e->getMessage(), [
                'host' => $this->config['host'],
                'trace' => $e->getTraceAsString(),
            ]);
            throw new Exception('Failed to establish EPP connection: '.$e->getMessage());
        }
    }

    /**
     * Check Domain Availability
     *
     * @throws Exception
     */
    public function checkDomain(array $domains): array
    {
        try {
            $this->ensureConnection();

            $frame = new CheckDomain;
            foreach ($domains as $domain) {
                $frame->addDomain($domain);
            }

            $response = $this->client->request($frame);
            if (! $response) {
                throw new Exception('No response received from EPP server');
            }

            $results = [];
            $data = $response->data();

            Log::debug('EPP Response Data:', ['data' => $data]);

            if (! empty($data) && isset($data['chkData']['cd'])) {
                // Handle both single and multiple domain responses
                $items = isset($data['chkData']['cd'][0]) ? $data['chkData']['cd'] : [$data['chkData']['cd']];

                foreach ($items as $item) {
                    // Extract domain name - handle both string and array formats
                    $domainName = $item['name']['_text'] ?? $item['name'];

                    // Extract availability - check both formats
                    $available = false;
                    if (isset($item['name']['@name']['avail'])) {
                        $available = $item['name']['@name']['avail'] === '1' || $item['name']['@name']['avail'] === true;
                    } elseif (isset($item['@name']['avail'])) {
                        $available = $item['@name']['avail'] === '1' || $item['@name']['avail'] === true;
                    }

                    Log::debug('Processing domain result:', [
                        'domainName' => $domainName,
                        'available' => $available,
                        'item' => $item,
                    ]);

                    $results[$domainName] = (object) [
                        'available' => $available,
                        'reason' => $item['reason'] ?? null,
                    ];
                }
            } else {
                Log::warning('Unexpected response structure:', ['data' => $data]);
            }

            return $results;
        } catch (Exception $e) {
            Log::error('Domain check error: '.$e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Check Contact availability
     *
     * @throws Exception
     */
    public function checkContacts(array $contactIds): CheckContact
    {
        try {
            $this->ensureConnection();
            $frame = new CheckContact;

            foreach ($contactIds as $contactId) {
                $frame->addId($contactId);
            }

            return $frame;

        } catch (Exception $e) {
            Log::error('Contact check failed: '.$e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Create Domain Contact
     *
     * @throws Exception
     */
    public function createContacts(array $contacts): array
    {
        try {
            $this->ensureConnection();
            $frame = new CreateContact;
            $frame->setId($contacts['id']);
            $frame->setName($contacts['name']);
            $frame->setOrganization($contacts['organization']);

            foreach ($contacts['streets'] as $street) {
                $frame->addStreet($street);
            }

            $frame->setCity($contacts['city']);
            $frame->setProvince($contacts['province']);
            $frame->setPostalCode($contacts['postal_code']);
            $frame->setCountryCode($contacts['country_code']);
            $frame->setVoice($contacts['voice']);
            $frame->setFax($contacts['fax']['number'], $contacts['fax']['ext']);
            $frame->setEmail($contacts['email']);

            $auth = $frame->setAuthInfo();

            foreach ($contacts['disclose'] as $item) {
                $frame->addDisclose($item);
            }

            return ['frame' => $frame, 'auth' => $auth];

        } catch (Exception $e) {
            Log::error('Contact creation failed: '.$e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }
    
    /**
     * Update Domain Contact in EPP registry
     *
     * @throws Exception
     */
    public function updateContact(string $contactId, array $contactData): array
    {
        try {
            $this->ensureConnection();
            
            // Import the UpdateContact class
            $updateContactClass = 'AfriCC\EPP\Frame\Command\Update\Contact';
            if (!class_exists($updateContactClass)) {
                throw new Exception("UpdateContact class not found in EPP library");
            }
            
            // Create update frame
            $frame = new $updateContactClass();
            $frame->setId($contactId);
            
            // Set contact information to update
            if (isset($contactData['name'])) {
                $frame->setChgName($contactData['name']);
            }
            
            if (isset($contactData['organization'])) {
                $frame->setChgOrganization($contactData['organization']);
            }
            
            // Handle address changes
            if (!empty($contactData['streets']) || 
                isset($contactData['city']) || 
                isset($contactData['province']) || 
                isset($contactData['postal_code']) || 
                isset($contactData['country_code'])) {
                
                // Add streets
                if (!empty($contactData['streets'])) {
                    foreach ($contactData['streets'] as $street) {
                        $frame->addChgStreet($street);
                    }
                }
                
                // Set other address fields
                if (isset($contactData['city'])) {
                    $frame->setChgCity($contactData['city']);
                }
                
                if (isset($contactData['province'])) {
                    $frame->setChgProvince($contactData['province']);
                }
                
                if (isset($contactData['postal_code'])) {
                    $frame->setChgPostalCode($contactData['postal_code']);
                }
                
                if (isset($contactData['country_code'])) {
                    $frame->setChgCountryCode($contactData['country_code']);
                }
            }
            
            // Set contact details
            if (isset($contactData['voice'])) {
                $frame->setChgVoice($contactData['voice']);
            }
            
            if (isset($contactData['fax'])) {
                $frame->setChgFax($contactData['fax']['number'], $contactData['fax']['ext'] ?? '');
            }
            
            if (isset($contactData['email'])) {
                $frame->setChgEmail($contactData['email']);
            }
            
            // Update disclosure preferences if provided
            if (!empty($contactData['disclose'])) {
                foreach ($contactData['disclose'] as $item) {
                    $frame->addChgDisclose($item);
                }
            }
            
            // Generate new auth info if requested
            $auth = null;
            if (!empty($contactData['generate_new_auth'])) {
                $auth = $frame->setChgAuthInfo();
            }
            
            return ['frame' => $frame, 'auth' => $auth];
            
        } catch (Exception $e) {
            Log::error('Contact update failed: ' . $e->getMessage(), [
                'contact_id' => $contactId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Check available hosts
     *
     * @throws Exception
     */
    public function checkHosts(array $hosts): CheckHost
    {
        try {
            $this->ensureConnection();
            $frame = new CheckHost;

            foreach ($hosts as $host) {
                $frame->addHost($host);
            }

            return $frame;
        } catch (Exception $e) {
            Log::error('Host check failed: '.$e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Create Hosts
     *
     * @throws Exception
     */
    public function createHost(string $host, array $addresses): CreateHost
    {
        try {
            $this->ensureConnection();
            $frame = new CreateHost;
            $frame->setHost($host);

            foreach ($addresses as $address) {
                $frame->addAddr($address);
            }

            return $frame;
        } catch (Exception $e) {
            Log::error('Host creation failed: '.$e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Create domain functionality
     *
     * @throws Exception
     */
    public function createDomain(string $domain, string $period, array $hostAttrs, string $registrant, string $adminContact, string $techContact, string $billingContact): CreateDomain
    {
        try {
            $this->ensureConnection();
            $frame = new CreateDomain;
            $frame->setDomain($domain);
            $frame->setPeriod($period);

            foreach ($hostAttrs as $host => $ips) {
                if (is_array($ips)) {
                    $frame->addHostAttr($host, $ips);
                } else {
                    $frame->addHostAttr($host);
                }
            }

            $frame->setRegistrant($registrant);
            $frame->setAdminContact($adminContact);
            $frame->setTechContact($techContact);
            $frame->setBillingContact($billingContact);

            $frame->setAuthInfo();

            return $frame;
        } catch (Exception $e) {
            Log::error('Domain creation failed: '.$e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Domain transfer
     *
     * @throws Exception
     */
    public function transferDomain(string $domain, string $operation, string $period, string $authInfo): TransferDomain
    {
        try {
            $this->ensureConnection();
            $frame = new TransferDomain;
            $frame->setOperation($operation);
            $frame->setDomain($domain);
            $frame->setPeriod($period);
            $frame->setAuthInfo($authInfo);

            return $frame;
        } catch (Exception $e) {
            Log::error('Domain transfer failed: '.$e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Renew Domain
     *
     * @throws Exception
     */
    public function renewDomain(string $domain, $currentExpirationDate, string $period): RenewDomain
    {
        try {
            $this->ensureConnection();

            // Format the expiration date to EPP standard format (YYYY-MM-DD)
            if ($currentExpirationDate instanceof DateTime) {
                $currentExpirationDate = $currentExpirationDate->format('Y-m-d');
            }

            $frame = new RenewDomain;
            $frame->setDomain($domain);
            $frame->setCurrentExpirationDate($currentExpirationDate);
            $frame->setPeriod($period);

            // Log the renewal attempt with configuration context
            Log::info('Attempting domain renewal', [
                'domain' => $domain,
                'expiration_date' => $currentExpirationDate,
                'period' => $period,
                'epp_host' => $this->config['host'],
            ]);

            return $frame;
        } catch (Exception $e) {
            Log::error('Domain renewal failed: '.$e->getMessage(), [
                'domain' => $domain,
                'expiration_date' => $currentExpirationDate,
                'period' => $period,
                'epp_host' => $this->config['host'],
                'trace' => $e->getTraceAsString(),
            ]);
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Delete Domain
     *
     * @throws Exception
     */
    public function deleteDomain(string $domain): DeleteDomain
    {
        try {
            $this->ensureConnection();
            $frame = new DeleteDomain;
            $frame->setDomain($domain);

            return $frame;
        } catch (Exception $e) {
            Log::error('Domain deletion failed: '.$e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Acknowledge domain messages
     *
     * @throws Exception
     */
    public function pollAcknowledge(string $messageId): Poll
    {
        try {
            $this->ensureConnection();
            $frame = new Poll;
            $frame->ack($messageId);

            return $frame;
        } catch (Exception $e) {
            Log::error('Poll acknowledge failed: '.$e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Update domain
     *
     * @throws Exception
     */
    public function updateDomain(string $domain, array $adminContacts, array $techContacts, array $hostObjs, array $hostAttrs, array $statuses, array $removeHostAttrs): array
    {
        try {
            $this->ensureConnection();
            $frame = new UpdateDomain;
            $frame->setDomain($domain);

            // Add section
            $addSection = false;

            if (! empty($adminContacts)) {
                foreach ($adminContacts as $contact) {
                    $frame->addAdminContact($contact);
                }
                $addSection = true;
            }

            if (! empty($techContacts)) {
                foreach ($techContacts as $contact) {
                    $frame->addTechContact($contact);
                }
                $addSection = true;
            }

            if (! empty($hostObjs)) {
                foreach ($hostObjs as $host) {
                    $frame->addHostObj($host);
                    $addSection = true;
                }
            }

            if (! empty($hostAttrs)) {
                foreach ($hostAttrs as $host => $ips) {
                    $frame->addHostAttr($host, $ips);
                    $addSection = true;
                }
            }

            if (! empty($statuses)) {
                foreach ($statuses as $status => $reason) {
                    $frame->addStatus($status, $reason);
                    $addSection = true;
                }
            }

            // Remove section
            $removeSection = false;

            if (! empty($removeHostAttrs)) {
                foreach ($removeHostAttrs as $host) {
                    $frame->removeHostObj($host);
                    $removeSection = true;
                }
            }

            // Only change authInfo if we're making changes
            $pw = null;
            if ($addSection || $removeSection) {
                $pw = $frame->changeAuthInfo();
            }

            Log::debug('EPP update domain frame created', [
                'domain' => $domain,
                'add_section' => $addSection,
                'remove_section' => $removeSection,
                'host_objs' => $hostObjs,
                'remove_hosts' => $removeHostAttrs,
            ]);

            return ['frame' => $frame, 'authInfo' => $pw];
        } catch (Exception $e) {
            Log::error('Domain update failed: '.$e->getMessage(), [
                'domain' => $domain,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Try to reconnect on next request
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
            $frame->setDomain($domain, 'all');
            $response = $this->getClient()->request($frame);

            // Validate response
            if (! ($response instanceof Response) || ! ($result = $response->results()[0])) {
                throw new Exception('Invalid response from registry');
            }

            // Check response status
            if ($result->code() < 1000 || $result->code() >= 2000) {
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
                    'admin' => ! empty($adminContacts) ? $adminContacts : ($data['admin'] ?? null),
                    'tech' => ! empty($techContacts) ? $techContacts : ($data['tech'] ?? null),
                    'billing' => ! empty($billingContacts) ? $billingContacts : ($data['billing'] ?? null),
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

    /**
     * Get the EPP client instance
     */
    public function getClient(): EPPClient
    {
        return $this->client;
    }

    /**
     * Destructor to ensure connection is closed
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Close the EPP connection
     */
    public function disconnect(): void
    {
        if (isset($this->client)) {
            $this->client->close();
            $this->connected = false;
        }
    }
}
