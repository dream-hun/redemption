<?php

declare(strict_types=1);

namespace App\Services\Epp\Frame\Command;

use AfriCC\EPP\Frame\Command as CommandFrame;

final class CheckDomain extends CommandFrame
{
    public function __construct()
    {
        parent::__construct();
        $this->set('check/domain:check', null);
    }

    public function addDomain(string $domain): void
    {
        $this->set('check/domain:check/domain:name', $domain);
    }
}
