<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Domain;
use Illuminate\Http\Request;
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
        $domain->load('registrantContact', 'adminContact', 'techContact', 'owner');
        $contacts = [
            'registrant' => $domain->registrantContact,
            'admin' => $domain->adminContact,
            'tech' => $domain->techContact
        ];

        return view('admin.domains.edit', compact('domain', 'countries', 'contacts'));
    }

    public function show(Domain $domain)
    {
        abort_if(Gate::denies('domain_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domain->load('registrantContact', 'adminContact', 'techContact', 'owner');

        return view('admin.domains.show', compact('domain'));
    }

    public function updateContact(Request $request, Domain $domain, $type, $contactId)
    {
        abort_if(Gate::denies('domain_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validTypes = ['registrant', 'admin', 'tech'];
        abort_if(!in_array($type, $validTypes), Response::HTTP_NOT_FOUND);

        $request->validate([
            'contact.name' => 'required|string|max:255',
            'contact.organization' => 'nullable|string|max:255',
            'contact.streets.*' => 'nullable|string|max:255',
            'contact.city' => 'required|string|max:255',
            'contact.province' => 'nullable|string|max:255',
            'contact.postal_code' => 'required|string|max:255',
            'contact.country_code' => 'required|exists:countries,code',
            'contact.voice' => 'required|string|max:255',
            'contact.email' => 'required|email|max:255',
        ]);

        $contactData = $request->input('contact');
        $contactData['street1'] = $contactData['streets'][0] ?? null;
        $contactData['street2'] = $contactData['streets'][1] ?? null;
        unset($contactData['streets']);

        $relationMap = [
            'registrant' => 'registrantContact',
            'admin' => 'adminContact',
            'tech' => 'techContact'
        ];

        $relation = $relationMap[$type];
        $domain->$relation()->where('id', $contactId)->update($contactData);

        return redirect()->back()->with('status', ucfirst($type) . ' contact information updated successfully.');
    }
}
