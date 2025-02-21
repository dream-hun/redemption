<?php

namespace App\Http\Controllers;

use App\Services\Epp\EppService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    protected $eppService;

    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    /**
     * Check contact availability
     */
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_ids' => 'required|array|min:1',
            'contact_ids.*' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $contactIds = $request->input('contact_ids');
            $frame = $this->eppService->checkContacts($contactIds);
            $response = $this->eppService->getClient()->request($frame);

            if ($response && $response->success()) {
                $data = $response->data();
                $results = [];

                if (! empty($data) && isset($data['chkData']['cd'])) {
                    // Ensure we have a consistent array structure even for single items
                    $items = isset($data['chkData']['cd'][0]) ? $data['chkData']['cd'] : [$data['chkData']['cd']];

                    foreach ($items as $cd) {
                        $contactId = $cd['id'];
                        $isAvailable = (bool) $cd['@id']['avail'];
                        $reason = $cd['reason'] ?? null;

                        $results[$contactId] = (object) [
                            'id' => $contactId,
                            'available' => $isAvailable,
                            'reason' => $reason,
                        ];
                    }
                }

                return response()->json(['results' => $results]);
            } else {
                $result = $response ? $response->results()[0] : null;

                return response()->json([
                    'error' => 'Contact check failed',
                    'code' => $result ? $result->code() : 'unknown',
                    'message' => $result ? $result->message() : 'No response',
                ], 400);
            }
        } catch (Exception $e) {
            Log::error('Contact availability check failed: '.$e->getMessage());

            return response()->json(['error' => 'Failed to check contact availability: '.$e->getMessage()], 500);
        }
    }

    /**
     * Create a new contact
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'organization' => 'sometimes|string|max:255',
            'streets' => 'required|array|min:1|max:3',
            'streets.*' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'sometimes|string|max:255',
            'postal_code' => 'required|string|max:16',
            'country_code' => 'required|string|size:2',
            'voice' => 'required|string',
            'fax' => 'sometimes|array',
            'fax.number' => 'required_with:fax|string',
            'fax.ext' => 'sometimes|string',
            'email' => 'required|email|max:255',
            'disclose' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            // First check if the contact ID is available
            $checkFrame = $this->eppService->checkContacts([$request->input('id')]);
            $checkResponse = $this->eppService->getClient()->request($checkFrame);

            if ($checkResponse && $checkResponse->success()) {
                $data = $checkResponse->data();
                if (! empty($data) && isset($data['chkData']['cd'])) {
                    $cd = $data['chkData']['cd'];
                    $isAvailable = (bool) $cd['@id']['avail'];

                    if (! $isAvailable) {
                        return response()->json([
                            'error' => 'Contact ID is not available',
                            'reason' => $cd['reason'] ?? 'Unknown reason',
                        ], 400);
                    }
                }
            }

            // Prepare contact data
            $contactData = [
                'id' => $request->input('id'),
                'name' => $request->input('name'),
                'organization' => $request->input('organization', ''),
                'streets' => $request->input('streets'),
                'city' => $request->input('city'),
                'province' => $request->input('province', ''),
                'postal_code' => $request->input('postal_code'),
                'country_code' => $request->input('country_code'),
                'voice' => $request->input('voice'),
                'fax' => $request->input('fax', ['number' => '', 'ext' => '']),
                'email' => $request->input('email'),
                'disclose' => $request->input('disclose', []),
            ];

            $result = $this->eppService->createContacts($contactData);
            $frame = $result['frame'];
            $authInfo = $result['auth'] ?? null;

            $response = $this->eppService->getClient()->request($frame);

            if ($response && $response->success()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Contact created successfully',
                    'data' => $response->data(),
                    'auth_info' => $authInfo,
                ]);
            } else {
                $result = $response ? $response->results()[0] : null;

                return response()->json([
                    'error' => 'Failed to create contact',
                    'code' => $result ? $result->code() : 'unknown',
                    'message' => $result ? $result->message() : 'No response',
                ], 400);
            }
        } catch (Exception $e) {
            Log::error('Contact creation failed: '.$e->getMessage());

            return response()->json(['error' => 'Failed to create contact: '.$e->getMessage()], 500);
        }
    }
}
