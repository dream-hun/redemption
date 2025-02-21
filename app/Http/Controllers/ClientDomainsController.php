<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use Illuminate\Support\Facades\Auth;

class ClientDomainsController extends Controller
{
    public function index()
    {
        $domains = Domain::where('owner_id', Auth::id())->get();

        return view('client.domains.index', compact('domains'));
    }
}
