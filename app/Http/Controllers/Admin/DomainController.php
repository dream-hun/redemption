<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Domain;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class DomainController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('domain_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domains = Domain::with(['registrantContact', 'adminContact', 'techContact', 'owner'])->get();

        return view('admin.domains.index', compact('domains'));
    }

    public function edit(Domain $domain)
    {
        abort_if(Gate::denies('domain_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $countries = Country::pluck('name', 'code');
        $domain->load('registrantContact', 'adminContact', 'techContact', 'owner', 'billingContact');
        $contacts = [
            'registrant' => $domain->registrantContact,
            'admin' => $domain->adminContact,
            'tech' => $domain->techContact,
            'billing' => $domain->billingContact,
        ];

        return view('admin.domains.edit', compact('domain', 'countries', 'contacts'));
    }

    public function show(Domain $domain)
    {
        abort_if(Gate::denies('domain_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domain->load('registrantContact', 'adminContact', 'techContact', 'owner');

        return view('admin.domains.show', compact('domain'));
    }
}
