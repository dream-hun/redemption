<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ContactController extends Controller
{
    /**
     * Get contact details.
     */
    public function details(Request $request, string $id): JsonResponse
    {
        try {
            $contact = Contact::findOrFail($id);

            return response()->json([
                'success' => true,
                'contact' => [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'email' => $contact->email,
                    'voice' => $contact->voice,
                    'organization' => $contact->organization,
                    'street1' => $contact->street1,
                    'city' => $contact->city,
                    'province' => $contact->province,
                    'country_code' => $contact->country_code,
                    'postal_code' => $contact->postal_code,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found',
            ], 404);
        }
    }
}
