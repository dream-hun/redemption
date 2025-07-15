<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Domain;

final class AccountController extends Controller
{
    public function __invoke()
    {
        $domains = Domain::where('owner_id', auth()->user()->id)->count();
        $hosts = Domain::where('owner_id', auth()->user()->id)->count();
        $vps = Domain::where('owner_id', auth()->user()->id)->count();
        $ssl = Domain::where('owner_id', auth()->user()->id)->count();

        return view('client.index', ['domains' => $domains, 'hosts' => $hosts, 'vps' => $vps, 'ssls' => $ssl]);
    }
}
