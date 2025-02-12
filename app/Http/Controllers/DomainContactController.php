<?php

namespace App\Http\Controllers;

use App\Services\Epp\EppService;
use Exception;
use Illuminate\Http\Request;

class DomainContactController extends Controller
{
    protected EppService $eppService;

    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    public function index()
    {
        return view('domains.create-contact');
    }

    public function store(Request $request)
    {
        $request->validate([
            'contact_id' => 'required|string|unique:domain_contacts,contact_id',
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
        ]);

        try {
            $contactData = [
                'id' => $request->contact_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'country' => $request->country,
            ];

            // Call EPP Service to create contact
            $this->eppService->createContacts($contactData);

            // Save to database
            DomainContact::create($contactData);

            return response()->json(['message' => 'Domain contact created successfully'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
