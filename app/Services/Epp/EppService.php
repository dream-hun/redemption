<?php

namespace App\Services\Epp;

use AfriCC\EPP\Client as EPPClient;
use AfriCC\EPP\Frame\Command\Check\Contact as CheckContact;
use AfriCC\EPP\Frame\Command\Check\Domain as CheckDomain;
use AfriCC\EPP\Frame\Command\Check\Host as CheckHost;
use AfriCC\EPP\Frame\Command\Create\Contact as CreateContact;
use AfriCC\EPP\Frame\Command\Create\Domain as CreateDomain;
use AfriCC\EPP\Frame\Command\Create\Host as CreateHost;
use AfriCC\EPP\Frame\Command\Delete\Contact as DeleteContact;
use AfriCC\EPP\Frame\Command\Delete\Domain as DeleteDomain;
use AfriCC\EPP\Frame\Command\Info\Contact as InfoContact;
use AfriCC\EPP\Frame\Command\Info\Domain as InfoDomain;
use AfriCC\EPP\Frame\Command\Poll;
use AfriCC\EPP\Frame\Command\Renew\Domain as RenewDomain;
use AfriCC\EPP\Frame\Command\Transfer\Domain as TransferDomain;
use AfriCC\EPP\Frame\Command\Update\Domain as UpdateDomain;
use AfriCC\EPP\Frame\Response;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EppService
{
    private EPPClient $client;

    private array $config;

    private bool $connected = false;

    private int $maxRetries = 3;

    private int $retryDelay = 1; // seconds

    /**
     * Delete a contact from the EPP registry
     *
     * @param  string  $contactId  The ID of the contact to delete
     * @return array Response from EPP server
     *
     * @throws Exception
     */
    public function deleteContact(string $contactId): array
    {
        try {
            $this->connect();

            $frame = new DeleteContact;
            $frame->setId($contactId);

            $response = $this->client->request($frame);

            // Handle response based on its type
            if (method_exists($response, 'getMessage')) {
                $message = $response->getMessage();
            } elseif (method_exists($response, 'resultMessage')) {
                $message = $response->resultMessage();
            } else {
                $message = 'Operation completed';
            }

            return [
                'success' => $response->success(),
                'message' => $message,
                'code' => $response->code(),
            ];
        } catch (Exception $e) {
            Log::error('EPP delete contact failed', [
                'contact_id' => $contactId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        }
    }

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
     * Get contact information from EPP registry
     *
     * @throws Exception
     */
    public function infoContact(string $contactId): ?array
    {
        try {
            $this->connect();

            $frame = new InfoContact;
            $frame->setId($contactId);

            Log::info('Sending contact info request to EPP', [
                'contact_id' => $contactId,
            ]);

            $response = $this->client->request($frame);

            if (! $response) {
                throw new Exception('No response received from EPP server');
            }

            $results = $response->results();
            if (empty($results)) {
                throw new Exception('Empty response from EPP server');
            }

            $result = $results[0];
            if ($result->code() !== 1000) {
                Log::error('Failed to get contact info from EPP', [
                    'contact_id' => $contactId,
                    'code' => $result->code(),
                    'message' => $result->message(),
                ]);

                return null;
            }

            $data = $response->data();
            $contactData = $data['infData'] ?? null;
            if (! $contactData) {
                Log::error('Invalid contact info response from EPP', [
                    'contact_id' => $contactId,
                    'data' => $data,
                ]);

                return null;
            }

            // Format contact data
            return [
                'contact' => [
                    'id' => $contactData['id'] ?? '',
                    'name' => $contactData['postalInfo']['name'] ?? '',
                    'organization' => $contactData['postalInfo']['org'] ?? '',
                    'streets' => $contactData['postalInfo']['addr']['street'] ?? [],
                    'city' => $contactData['postalInfo']['addr']['city'] ?? '',
                    'province' => $contactData['postalInfo']['addr']['sp'] ?? '',
                    'postal_code' => $contactData['postalInfo']['addr']['pc'] ?? '',
                    'country_code' => $contactData['postalInfo']['addr']['cc'] ?? '',
                    'voice' => $contactData['voice'] ?? '',
                    'fax' => [
                        'number' => $contactData['fax'] ?? '',
                        'ext' => $contactData['faxExt'] ?? '',
                    ],
                    'email' => $contactData['email'] ?? '',
                    'status' => $contactData['status'] ?? [],
                    'auth_info' => $contactData['authInfo']['pw'] ?? '',
                ],
            ];

        } catch (Exception $e) {
            Log::error('Exception while getting contact info from EPP', [
                'contact_id' => $contactId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
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
    public function checkContacts(array $contactIds): array
    {
        try {
            $this->ensureConnection();
            $frame = new CheckContact;

            foreach ($contactIds as $contactId) {
                $frame->addId($contactId);
            }

            $response = $this->client->request($frame);
            if (! $response) {
                throw new Exception('No response received from EPP server');
            }

            $results = [];
            $data = $response->data();

            Log::debug('EPP Contact Check Response:', ['data' => $data]);

            if (! empty($data) && isset($data['chkData'])) {
                // Handle both single and multiple contact responses
                $items = $data['chkData']['cd'] ?? [];
                if (! is_array($items) || ! isset($items[0])) {
                    $items = [$items];
                }

                foreach ($items as $item) {
                    // Extract contact ID and availability
                    $contactId = null;
                    $available = false;

                    if (isset($item['id'])) {
                        if (is_array($item['id']) && isset($item['id']['_text'])) {
                            $contactId = $item['id']['_text'];
                            $available = ($item['id']['@attributes']['avail'] ?? '') === '1';
                        } else {
                            $contactId = $item['id'];
                            $available = true; // If no explicit availability, assume available
                        }
                    }

                    if ($contactId) {
                        $results[$contactId] = (object) [
                            'available' => $available,
                            'reason' => $item['reason'] ?? null,
                        ];
                    }
                }
            }

            return $results;

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

            Log::debug('Creating contact with data:', ['contacts' => $contacts]);

            // Validate required contact data
            if (! is_array($contacts)) {
                throw new Exception('Invalid contact data: must be an array');
            }

            $requiredFields = ['contact_id', 'name', 'street1', 'city', 'country_code', 'voice', 'email'];
            foreach ($requiredFields as $field) {
                if (empty($contacts[$field])) {
                    throw new Exception("Invalid contact data: {$field} is required");
                }
                if (! is_string($contacts[$field])) {
                    throw new Exception("Invalid contact data: {$field} must be a string");
                }
            }

            $frame = new CreateContact;
            $frame->setId($contacts['contact_id']);
            $frame->setName($contacts['name']);

            if (! empty($contacts['organization'])) {
                if (! is_string($contacts['organization'])) {
                    throw new Exception('Invalid contact data: organization must be a string');
                }
                $frame->setOrganization($contacts['organization']);
            }

            // Handle street addresses
            $frame->addStreet($contacts['street1']);

            if (! empty($contacts['street2'])) {
                if (! is_string($contacts['street2'])) {
                    throw new Exception('Invalid contact data: street2 must be a string');
                }
                $frame->addStreet($contacts['street2']);
            }

            // Validate and set required city
            if (empty($contacts['city'])) {
                throw new Exception('Invalid contact data: city is required');
            }
            $frame->setCity($contacts['city']);

            // Optional province
            if (! empty($contacts['province'])) {
                $frame->setProvince($contacts['province']);
            }

            // Optional postal code
            if (! empty($contacts['postal_code'])) {
                $frame->setPostalCode($contacts['postal_code']);
            }

            // Validate and set required country code
            if (empty($contacts['country_code'])) {
                throw new Exception('Invalid contact data: country_code is required');
            }
            $frame->setCountryCode($contacts['country_code']);

            // Format phone number to EPP format (+CC.number)
            if (empty($contacts['voice'])) {
                throw new Exception('Invalid contact data: voice (phone) is required');
            }

            $phone = $contacts['voice'];
            if (! is_string($phone)) {
                throw new Exception('Invalid contact data: voice (phone) must be a string');
            }

            if (! str_starts_with($phone, '+')) {
                // Add country code for Rwanda if not present
                $phone = '+250.'.ltrim($phone, '0');
            }
            $frame->setVoice($phone);

            // Handle optional fax
            if (! empty($contacts['fax'])) {
                $fax = $contacts['fax'];
                if (! is_string($fax)) {
                    throw new Exception('Invalid contact data: fax must be a string');
                }

                if (! str_starts_with($fax, '+')) {
                    $fax = '+250.'.ltrim($fax, '0');
                }
                $frame->setFax($fax, $contacts['fax_ext'] ?? '');
            }

            // Validate and set required email
            if (empty($contacts['email'])) {
                throw new Exception('Invalid contact data: email is required');
            }
            if (! is_string($contacts['email'])) {
                throw new Exception('Invalid contact data: email must be a string');
            }
            $frame->setEmail($contacts['email']);

            $auth = $frame->setAuthInfo();

            // Send the frame and get response
            $response = $this->client->request($frame);

            if (! $response) {
                throw new Exception('No response received from EPP server');
            }

            $results = $response->results();
            if (empty($results)) {
                throw new Exception('Empty response from EPP server');
            }

            $result = $results[0];
            if ($result->code() !== 1000) {
                Log::error('Failed to create contact in EPP', [
                    'code' => $result->code(),
                    'message' => $result->message(),
                ]);
                throw new Exception('Failed to create contact in EPP registry: '.$result->message());
            }

            Log::info('Contact created successfully in EPP', [
                'contact_id' => $contacts['contact_id'],
                'code' => $result->code(),
                'message' => $result->message(),
            ]);

            return [
                'contact_id' => $contacts['contact_id'],
                'auth' => $auth,
                'code' => $result->code(),
                'message' => $result->message(),
            ];

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
     * @param  string  $contactId  The ID of the contact to update
     * @param  array  $contactData  Contact data to update
     * @return array Response from EPP server
     *
     * @throws Exception
     */
    public function updateContact(string $contactId, array $contactData): array
    {
        try {
            $this->ensureConnection();

            // Create update frame
            $frame = new \AfriCC\EPP\Frame\Command\Update\Contact;
            $frame->setId($contactId);

            // Set contact information to update
            if (isset($contactData['name'])) {
                $frame->setChgName($contactData['name']);
            }

            if (isset($contactData['organization'])) {
                $frame->setChgOrganization($contactData['organization']);
            }

            // Handle address changes
            if (! empty($contactData['streets']) ||
                isset($contactData['city']) ||
                isset($contactData['province']) ||
                isset($contactData['postal_code']) ||
                isset($contactData['country_code'])) {

                // Add streets
                if (! empty($contactData['streets'])) {
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
            if (! empty($contactData['disclose'])) {
                foreach ($contactData['disclose'] as $item) {
                    $frame->addChgDisclose($item);
                }
            }

            // Generate new auth info if requested
            $auth = null;
            if (! empty($contactData['generate_new_auth'])) {
                $auth = $frame->setChgAuthInfo();
            }

            // Send the request and get the response
            $response = $this->client->request($frame);

            if (! $response) {
                throw new Exception('No response received from EPP server');
            }

            $results = $response->results();
            if (empty($results)) {
                throw new Exception('Empty response from EPP server');
            }

            $result = $results[0];

            // Handle response based on its type
            if (method_exists($response, 'getMessage')) {
                $message = $response->getMessage();
            } elseif (method_exists($result, 'message')) {
                $message = $result->message();
            } else {
                $message = 'Operation completed';
            }

            Log::info('Contact updated successfully in EPP', [
                'id' => $contactId,
                'code' => $result->code(),
                'message' => $message,
            ]);

            return [
                'success' => $result->code() === 1000,
                'message' => $message,
                'code' => $result->code(),
                'auth' => $auth,
            ];

        } catch (Exception $e) {
            Log::error('Contact update failed: '.$e->getMessage(), [
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
    public function createDomain(string $domain, string $period, array $nameservers, string $registrant, string $adminContact, string $techContact, string $billingContact): CreateDomain
    {
        try {
            $this->ensureConnection();
            $frame = new CreateDomain;
            $frame->setDomain($domain);
            $frame->setPeriod($period);

            // Use hostObj instead of hostAttr as required by the EPP server
            foreach ($nameservers as $host) {
                if (! empty($host)) {
                    // Make sure the host is properly formatted
                    $host = trim($host);
                    if (! empty($host)) {
                        // Log the nameserver being added
                        Log::info('Adding nameserver to domain', [
                            'domain' => $domain,
                            'nameserver' => $host,
                        ]);
                        $frame->addHostObj($host);
                    }
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

            // CRITICAL: For EPP renewal, we MUST use EXACTLY the date format provided by the registry
            // The registry expects the exact same string it provided, including full ISO format with timezone
            // Example: "2027-02-28T06:32:27.850Z" - NOT just "2027-02-28"

            // Only format if it's a DateTime object (should be avoided for renewals)
            if ($currentExpirationDate instanceof \DateTime) {
                // Use ISO 8601 format with timezone to match registry format
                $currentExpirationDate = $currentExpirationDate->format(\DateTime::ISO8601);
                Log::warning('Converting DateTime to string for EPP renewal - this may cause issues', [
                    'domain' => $domain,
                    'formatted_date' => $currentExpirationDate,
                ]);
            } elseif (! is_string($currentExpirationDate)) {
                // If we received something unexpected (not string or DateTime), use current date
                Log::error('Unexpected date type for renewal, using ISO format', [
                    'domain' => $domain,
                    'date_type' => gettype($currentExpirationDate),
                ]);
                $currentExpirationDate = (new \DateTime)->format(\DateTime::ISO8601);
            } else {
                // It's already a string - log but don't modify at all
                Log::info('Using exact registry date string for EPP renewal', [
                    'domain' => $domain,
                    'raw_date' => $currentExpirationDate,
                ]);
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
     * Update domain nameservers
     *
     * @param  string  $domain  Domain name
     * @param  array  $nameservers  Array of nameserver hostnames
     * @return UpdateDomain EPP frame
     *
     * @throws Exception
     */
    public function updateDomainNameservers(string $domain, array $nameservers): UpdateDomain
    {
        try {
            $this->ensureConnection();

            // Normalize domain name (remove trailing dot if present)
            $domain = rtrim($domain, '.');

            // Filter out empty nameservers and normalize hostnames
            $nameservers = array_filter(array_map(function ($ns) {
                // Normalize nameserver hostname (remove trailing dot if present)
                return rtrim(trim($ns), '.');
            }, $nameservers), fn ($ns) => ! empty($ns));

            // Get current nameservers for the domain
            $infoFrame = new InfoDomain;
            $infoFrame->setDomain($domain);
            $infoResponse = $this->client->request($infoFrame);

            // Create update domain frame
            $frame = new UpdateDomain;
            $frame->setDomain($domain);

            // First, check if the nameservers exist in the registry
            // If they don't exist, we need to create them first
            foreach ($nameservers as $ns) {
                // Check if the nameserver exists
                $checkFrame = new \AfriCC\EPP\Frame\Command\Check\Host;
                $checkFrame->addHost($ns);
                $checkResponse = $this->client->request($checkFrame);

                if ($checkResponse->code() === 1000) {
                    // Parse the response to see if the host exists
                    $responseXml = (string) $checkResponse;

                    // If the host doesn't exist and contains the domain we're updating,
                    // we need to create it as a subordinate host
                    if (strpos($responseXml, '<host:name avail="1">') !== false &&
                        strpos($ns, $domain) !== false) {

                        Log::info("Creating subordinate host: {$ns}");

                        // Create the host
                        $createFrame = new \AfriCC\EPP\Frame\Command\Create\Host;
                        $createFrame->setHost($ns);

                        // Add a default IP address for the host
                        // This is required by some EPP registries for subordinate hosts
                        $createFrame->addAddr('127.0.0.1');

                        $createResponse = $this->client->request($createFrame);

                        if ($createResponse->code() !== 1000) {
                            Log::warning("Failed to create host {$ns}: {$createResponse->message()}");
                        } else {
                            Log::info("Successfully created host {$ns}");
                        }
                    }
                }
            }

            // Now update the domain with the new nameservers
            // Following the example from the PHP-EPP2 library

            // First, if we can get the current nameservers, remove them
            if ($infoResponse->code() === 1000) {
                $responseXml = (string) $infoResponse;

                // Extract current nameservers using regex
                // This is a simple approach since we can't use XPath directly
                preg_match_all('/<domain:hostObj>([^<]+)<\/domain:hostObj>/', $responseXml, $matches);

                if (! empty($matches[1])) {
                    foreach ($matches[1] as $currentNs) {
                        Log::info("Removing nameserver: {$currentNs}");
                        $frame->removeHostObj($currentNs);
                    }
                }
            }

            // Add the new nameservers
            foreach ($nameservers as $ns) {
                Log::info("Adding nameserver: {$ns}");
                $frame->addHostObj($ns);
            }

            // Change auth info (optional but recommended for security)
            $authInfo = Str::random(12);
            $frame->changeAuthInfo($authInfo);

            // Log the frame for debugging
            Log::debug('EPP update domain nameservers frame created', [
                'domain' => $domain,
                'new_nameservers' => $nameservers,
                'frame' => (string) $frame,
            ]);

            return $frame;
        } catch (Exception $e) {
            Log::error('EPP update domain nameservers error', [
                'domain' => $domain,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Update domain contacts
     *
     * @param  string  $domain  Domain name
     * @param  array  $contactData  Array of contacts by type (registrant, admin, tech, billing)
     * @param  array  $contactsToRemove  Array of contact types to remove
     * @return UpdateDomain EPP frame
     *
     * @throws Exception
     */
    public function updateDomainContacts(string $domain, array $contactData, array $contactsToRemove = []): UpdateDomain
    {
        try {
            $this->ensureConnection();

            // Normalize domain name (remove trailing dot if present)
            $domain = rtrim($domain, '.');

            // Create update domain frame
            $frame = new UpdateDomain;
            $frame->setDomain($domain);

            // Get current domain info to identify existing contacts
            $infoFrame = new InfoDomain;
            $infoFrame->setDomain($domain);
            $infoResponse = $this->client->request($infoFrame);
            $currentContacts = [];

            if ($infoResponse->code() === 1000) {
                $responseXml = (string) $infoResponse;

                // Extract current contacts using regex
                preg_match_all('/<domain:contact type="([^"]+)">([^<]+)<\/domain:contact>/', $responseXml, $matches, PREG_SET_ORDER);

                foreach ($matches as $match) {
                    $contactType = strtolower($match[1]);
                    $contactId = $match[2];
                    $currentContacts[$contactType] = $contactId;
                }

                // Extract registrant
                preg_match('/<domain:registrant>([^<]+)<\/domain:registrant>/', $responseXml, $registrantMatch);
                if (! empty($registrantMatch[1])) {
                    $currentContacts['registrant'] = $registrantMatch[1];
                }
            }

            // Process contacts to add, update, or remove
            // First, handle the registrant contact if provided
            if (isset($contactData['registrant'])) {
                Log::info("Setting registrant contact: {$contactData['registrant']}");
                $frame->changeRegistrant($contactData['registrant']);
            }

            // Handle admin contacts
            if (isset($contactData['admin'])) {
                Log::info("Adding admin contact: {$contactData['admin']}");
                $frame->addAdminContact($contactData['admin']);
            } elseif (in_array('admin', $contactsToRemove) && isset($currentContacts['admin'])) {
                Log::info("Removing admin contact: {$currentContacts['admin']}");
                $frame->removeAdminContact($currentContacts['admin']);
            }

            // Handle tech contacts
            if (isset($contactData['tech'])) {
                Log::info("Adding tech contact: {$contactData['tech']}");
                $frame->addTechContact($contactData['tech']);
            } elseif (in_array('tech', $contactsToRemove) && isset($currentContacts['tech'])) {
                Log::info("Removing tech contact: {$currentContacts['tech']}");
                $frame->removeTechContact($currentContacts['tech']);
            }

            // Handle billing contacts
            if (isset($contactData['billing'])) {
                Log::info("Adding billing contact: {$contactData['billing']}");
                $frame->addBillingContact($contactData['billing']);
            } elseif (in_array('billing', $contactsToRemove) && isset($currentContacts['billing'])) {
                Log::info("Removing billing contact: {$currentContacts['billing']}");
                $frame->removeBillingContact($currentContacts['billing']);
            }

            // Change auth info (optional but recommended for security)
            $authInfo = Str::random(12);
            $frame->changeAuthInfo($authInfo);

            // Log the frame for debugging
            Log::debug('EPP update domain contacts frame created', [
                'domain' => $domain,
                'contact_data' => $contactData,
                'contacts_to_remove' => $contactsToRemove,
                'frame' => (string) $frame,
            ]);

            return $frame;
        } catch (Exception $e) {
            Log::error('EPP update domain contacts error', [
                'domain' => $domain,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
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
            $frame->setDomain($domain);

            // Log request for debugging
            Log::debug('Sending domain info request', ['domain' => $domain]);

            $response = $this->getClient()->request($frame);

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
