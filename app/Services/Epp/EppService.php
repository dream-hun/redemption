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
use AfriCC\EPP\Frame\Command\Poll;
use AfriCC\EPP\Frame\Command\Renew\Domain as RenewDomain;
use AfriCC\EPP\Frame\Command\Transfer\Domain as TransferDomain;
use AfriCC\EPP\Frame\Command\Update\Domain as UpdateDomain;
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
                Log::warning("EPP Client initialization attempt {$attempts} failed: " . $e->getMessage());
                
                if ($attempts < $this->maxRetries) {
                    sleep($this->retryDelay);
                }
            }
        }

        Log::error('EPP Client initialization failed after ' . $this->maxRetries . ' attempts');
        throw $lastException;
    }

    /**
     * Connect to the EPP server with retries
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
                Log::warning("EPP Connection attempt {$attempts} failed: " . $e->getMessage());
                
                if ($attempts < $this->maxRetries) {
                    sleep($this->retryDelay);
                }
            }
        }

        Log::error('EPP Connection failed after ' . $this->maxRetries . ' attempts');
        throw $lastException;
    }

    /**
     * Check if client is connected and try to reconnect if not
     */
    private function ensureConnection(): void
    {
        if (!$this->connected) {
            $this->connect();
        }
    }

    /**
     * Check Domain Availability
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
            if (!$response) {
                throw new Exception('No response received from EPP server');
            }

            $results = [];
            $data = $response->data();
            
            Log::debug('EPP Response Data:', ['data' => $data]);

            if (!empty($data) && isset($data['chkData']['cd'])) {
                // Handle both single and multiple domain responses
                $items = isset($data['chkData']['cd'][0]) ? $data['chkData']['cd'] : [$data['chkData']['cd']];
                
                foreach ($items as $item) {
                    // Extract domain name - handle both string and array formats
                    $domainName = isset($item['name']['_text']) ? $item['name']['_text'] : $item['name'];
                    
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
                        'item' => $item
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
            Log::error('Domain check error: ' . $e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Check Contact availability
     */
    public function checkContacts(array $contactIds)
    {
        try {
            $this->ensureConnection();
            $frame = new CheckContact;

            foreach ($contactIds as $contactId) {
                $frame->addId($contactId);
            }

            return $frame;

        } catch (Exception $e) {
            Log::error('Contact check failed: ' . $e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Create Domain Contact
     */
    public function createContacts(array $contacts)
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
            Log::error('Contact creation failed: ' . $e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Check available hosts
     */
    public function checkHosts(array $hosts)
    {
        try {
            $this->ensureConnection();
            $frame = new CheckHost;

            foreach ($hosts as $host) {
                $frame->addHost($host);
            }

            return $frame;
        } catch (Exception $e) {
            Log::error('Host check failed: ' . $e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Create Hosts
     */
    public function createHost(string $host, array $addresses)
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
            Log::error('Host creation failed: ' . $e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Create domain functionality
     *
     * @return void
     */
    public function createDomain(string $domain, string $period, array $hostAttrs, string $registrant, string $adminContact, string $techContact)
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
            $frame->setAuthInfo();

            return $frame;
        } catch (Exception $e) {
            Log::error('Domain creation failed: ' . $e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Domain transfer
     */
    public function transferDomain(string $domain, string $operation, string $period, string $authInfo)
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
            Log::error('Domain transfer failed: ' . $e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Renew Domain
     */
    public function renewDomain(string $domain, string $currentExpirationDate, string $period)
    {
        try {
            $this->ensureConnection();
            $frame = new RenewDomain;
            $frame->setDomain($domain);
            $frame->setCurrentExpirationDate($currentExpirationDate);
            $frame->setPeriod($period);

            return $frame;
        } catch (Exception $e) {
            Log::error('Domain renewal failed: ' . $e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Delete Domain
     */
    public function deleteDomain(string $domain)
    {
        try {
            $this->ensureConnection();
            $frame = new DeleteDomain;
            $frame->setDomain($domain);

            return $frame;
        } catch (Exception $e) {
            Log::error('Domain deletion failed: ' . $e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Acknowledge domain messages
     *
     * @return void
     */
    public function pollAcknowledge(string $messageId)
    {
        try {
            $this->ensureConnection();
            $frame = new Poll;
            $frame->ack($messageId);

            return $frame;
        } catch (Exception $e) {
            Log::error('Poll acknowledge failed: ' . $e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Update domain
     */
    public function updateDomain(string $domain, array $adminContacts, array $techContacts, array $hostObjs, array $hostAttrs, array $statuses, array $removeHostAttrs)
    {
        try {
            $this->ensureConnection();
            $frame = new UpdateDomain;
            $frame->setDomain($domain);

            foreach ($adminContacts as $contact) {
                $frame->addAdminContact($contact);
            }

            foreach ($techContacts as $contact) {
                $frame->addTechContact($contact);
            }

            foreach ($hostObjs as $host) {
                $frame->addHostObj($host);
            }

            foreach ($hostAttrs as $host => $ips) {
                $frame->addHostAttr($host, $ips);
            }

            foreach ($statuses as $status => $reason) {
                $frame->addStatus($status, $reason);
            }

            foreach ($removeHostAttrs as $host) {
                $frame->removeHostAttr($host);
            }

            $pw = $frame->changeAuthInfo();

            return ['frame' => $frame, 'authInfo' => $pw];
        } catch (Exception $e) {
            Log::error('Domain update failed: ' . $e->getMessage());
            // Try to reconnect on next request
            $this->connected = false;
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
