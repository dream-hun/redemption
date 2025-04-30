<?php

namespace App\Services\Epp\Frame\Command;

use AfriCC\EPP\Frame\Command as CommandFrame;

class CheckDomain extends CommandFrame
{
    public function __construct()
    {
        parent::__construct();
        $this->set('check/domain:check', null, [
            'xmlns:domain' => 'urn:ietf:params:xml:ns:domain-1.0',
        ]);
    }

    public function addDomain(string $domain): void
    {
        $this->set('check/domain:check/domain:name', $domain);
    }
}