<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Domain;

final class MyDomainController extends Controller
{
    public function index()
    {
        $domains = Domain::select(['id', 'uuid', 'name', 'registered_at', 'expires_at'])->where('owner_id', auth()->user()->id)->get();

        return view('account.domains.index', ['domains' => $domains]);

    }
}
