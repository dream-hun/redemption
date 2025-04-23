<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Services\Epp\EppService;
use Exception;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class ClientDomainsController extends Controller
{
    public function index()
    {
        $domains = Domain::where('owner_id', Auth::id())->get();

        return view('client.domains.index', ['domains' => $domains]);
    }

    public function manage(Domain $domain)
    {
        return view('client.domains.manage', ['domain' => $domain]);
    }

    public function destroy(HttpRequest $request, Domain $domain, EppService $eppService): RedirectResponse
    {
        // Validate the password
        $request->validate([
            'password' => ['required', function ($attribute, $value, $fail): void {
                if (! Hash::check($value, Auth::user()->password)) {
                    $fail('The provided password is incorrect.');
                }
            }],
        ]);

        try {
            // Delete domain via EPP
            $frame = $eppService->deleteDomain($domain->name);
            $response = $eppService->getClient()->request($frame);

            if (! $response || ! $response->success()) {
                throw new Exception('Failed to delete domain via EPP');
            }

            // Get contacts before deleting the domain
            $contacts = [
                $domain->registrant_contact_id,
                $domain->admin_contact_id,
                $domain->tech_contact_id,
            ];

            // Delete the domain from database
            $domain->delete();

            // Delete the contacts that are no longer used
            foreach ($contacts as $contactId) {
                if ($contactId) {
                    $contact = \App\Models\Contact::find($contactId);
                    if ($contact && ! $contact->domains()->exists()) {
                        $contact->delete();
                    }
                }
            }

            return redirect()->route('client.domains')->with('success', 'Domain and associated contacts deleted successfully!');
        } catch (Exception $e) {
            Log::error('Domain deletion failed: '.$e->getMessage());

            return redirect()->route('client.domains')->with('error', 'Failed to delete domain. Please try again later.');
        }
    }
}
