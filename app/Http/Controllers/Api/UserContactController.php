<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserContactController extends Controller
{
    /**
     * Get all contacts for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $contacts = Contact::where('user_id', auth()->id())
            ->select('id', 'uuid', 'contact_id', 'name', 'organization', 'email', 'contact_type')
            ->get();

        return response()->json([
            'success' => true,
            'contacts' => $contacts,
        ]);
    }
}
