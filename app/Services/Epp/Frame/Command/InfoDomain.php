<?php

namespace App\Services\Epp\Frame\Command;

use AfriCC\EPP\Frame\Command as CommandFrame;

class InfoDomain extends CommandFrame
{
    public function __construct()
    {
        parent::__construct();
        $this->set('info');
    }

    public function setDomain(string $domain): void
    {
        $this->set('info/domain:name', $domain);
    }
}