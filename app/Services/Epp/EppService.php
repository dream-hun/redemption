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

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->config = config('epp');

        if (! $this->config) {
            throw new Exception('EPP configuration not found');
        }

        // Debug log the configuration
        Log::debug('EPP Configuration:', [
            'host' => $this->config['host'] ?? 'not set',
            'username' => $this->config['username'] ?? 'not set',
            'port' => $this->config['port'] ?? 'not set',
            'ssl' => $this->config['ssl'] ?? 'not set',
            'debug' => $this->config['debug'] ?? 'not set',
        ]);

        if (empty($this->config['host'])) {
            throw new Exception('EPP host is not configured. Please set EPP_HOST in your .env file.');
        }

        if (empty($this->config['certificate']) || ! file_exists($this->config['certificate'])) {
            throw new Exception('EPP certificate not found. Please check the certificate path in your configuration.');
        }

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
        ];

        Log::debug('EPP Client Config:', $config);

        try {
            $this->client = new EPPClient($config);
        } catch (Exception $e) {
            Log::error('EPP Client initialization error: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Connect to the EPP server
     *
     * @return string|null The greeting message from the server
     *
     * @throws Exception If connection fails
     */
    public function connect(): ?string
    {
        try {
            return $this->client->connect();
        } catch (Exception $e) {
            Log::error('EPP Connection failed: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Close the EPP connection
     */
    public function disconnect(): void
    {
        if (isset($this->client)) {
            $this->client->close();
        }
    }

    /**
     * Check Domain Availability
     */
    public function checkDomain(array $domains)
    {
        try {
            Log::debug('Starting domain check for domains:', $domains);
            
            // Ensure we're connected before making the request
            $this->connect();
            Log::debug('Successfully connected to EPP server');

            $frame = new CheckDomain;
            foreach ($domains as $domain) {
                $frame->addDomain($domain);
            }
            Log::debug('Created check domain frame');

            $response = $this->client->request($frame);
            Log::debug('Received response from EPP server', ['success' => $response ? $response->success() : false]);
            
            $results = [];

            if ($response && $response->success()) {
                $data = $response->data();
                
                if (!empty($data) && is_array($data) && isset($data['chkData']['cd'])) {
                    // Ensure we have a consistent array structure even for single items
                    $items = isset($data['chkData']['cd'][0]) ? $data['chkData']['cd'] : [$data['chkData']['cd']];
                    
                    foreach ($items as $cd) {
                        $domainName = $cd['name'];
                        $isAvailable = (bool)$cd['@name']['avail'];
                        $reason = $cd['reason'] ?? null;
                        
                        $results[$domainName] = (object) [
                            'name' => $domainName,
                            'available' => $isAvailable,
                            'reason' => $reason
                        ];
                        
                        Log::debug('Processed domain result', [
                            'domain' => $domainName,
                            'available' => $isAvailable,
                            'reason' => $reason
                        ]);
                    }
                } else {
                    Log::warning('No check data in response', ['data' => $data]);
                }
            } else {
                $result = $response ? $response->results()[0] : null;
                Log::error('EPP response was not successful', [
                    'code' => $result ? $result->code() : 'unknown',
                    'message' => $result ? $result->message() : 'No response'
                ]);
            }

            // Disconnect after we're done
            $this->disconnect();
            Log::debug('Successfully disconnected from EPP server');

            return $results;
        } catch (Exception $e) {
            // Make sure to disconnect even if there's an error
            $this->disconnect();
            Log::error('Domain check failed: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Check Contact availability
     */
    public function checkContacts(array $contactIds)
    {
        try {
            $this->connect();
            $frame = new CheckContact;

            foreach ($contactIds as $contactId) {
                $frame->addId($contactId);
            }

            return $frame;

        } catch (Exception $e) {
            $this->disconnect();
            Log::error('Contact check failed: '.$e->getMessage());
            throw $e;
        }

    }

    /**
     * Create Domain Contact
     */
    public function createContacts(array $contacts)
    {
        try {
            $this->connect();
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
            // Make sure to disconnect even if there's an error
            $this->disconnect();
            Log::error('Contact creation failed: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Check available hosts
     */
    public function checkHosts(array $hosts)
    {
        $this->connect();
        $frame = new CheckHost;

        foreach ($hosts as $host) {
            $frame->addHost($host);
        }

        return $frame;
    }

    /**
     * Create Hosts
     */
    public function createHost(string $host, array $addresses)
    {
        $this->connect();
        $frame = new CreateHost;
        $frame->setHost($host);

        foreach ($addresses as $address) {
            $frame->addAddr($address);
        }

        return $frame;
    }

    /**
     * Create domain functionality
     *
     * @return void
     */
    public function createDomain(string $domain, string $period, array $hostAttrs, string $registrant, string $adminContact, string $techContact)
    {
        $this->connect();
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
    }

    /**
     * Domain transfer
     */
    public function transferDomain(string $domain, string $operation, string $period, string $authInfo)
    {
        $this->connect();
        $frame = new TransferDomain;
        $frame->setOperation($operation);
        $frame->setDomain($domain);
        $frame->setPeriod($period);
        $frame->setAuthInfo($authInfo);

        return $frame;
    }

    /**
     * Renew Domain
     */
    public function renewDomain(string $domain, string $currentExpirationDate, string $period)
    {
        $this->connect();
        $frame = new RenewDomain;
        $frame->setDomain($domain);
        $frame->setCurrentExpirationDate($currentExpirationDate);
        $frame->setPeriod($period);

        return $frame;
    }

    /**
     * Delete Domain
     */
    public function deleteDomain(string $domain)
    {
        $this->connect();
        $frame = new DeleteDomain;
        $frame->setDomain($domain);

        return $frame;
    }

    /**
     * Acknowledge domain messages
     *
     * @return void
     */
    public function pollAcknowledge(string $messageId)
    {
        $this->connect();
        $frame = new Poll;
        $frame->ack($messageId);

        return $frame;
    }

    /**
     * Update domain
     */
    public function updateDomain(string $domain, array $adminContacts, array $techContacts, array $hostObjs, array $hostAttrs, array $statuses, array $removeHostAttrs)
    {
        $this->connect();
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
}
