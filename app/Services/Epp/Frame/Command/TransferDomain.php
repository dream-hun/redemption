<?php

declare(strict_types=1);

namespace App\Services\Epp\Frame\Command;

use AfriCC\EPP\Frame\Command as CommandFrame;

final class TransferDomain extends CommandFrame
{
    public function __construct()
    {
        parent::__construct();
        $this->set('transfer', ['op' => 'request']);
    }

    public function setDomain(string $domain): void
    {
        $this->set('transfer/domain:name', $domain);
    }

    public function setAuthInfo(string $authInfo): void
    {
        $this->set('transfer/domain:authInfo/pw', $authInfo);
    }

    public function setPeriod(string $period): void
    {
        $this->set('transfer/domain:period', $period);
        $this->set('transfer/domain:period/@unit', 'y');
    }
}
