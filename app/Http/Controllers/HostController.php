<?php

namespace App\Http\Controllers;

use App\Services\Epp\EppService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HostController extends Controller
{
    protected $eppService;

    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    /**
     * Check host availability
     */
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hosts' => 'required|array|min:1',
            'hosts.*' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $hosts = $request->input('hosts');
            $frame = $this->eppService->checkHosts($hosts);
            $response = $this->eppService->getClient()->request($frame);

            if ($response && $response->success()) {
                $data = $response->data();
                $results = [];

                if (! empty($data) && isset($data['chkData']['cd'])) {
                    // Ensure we have a consistent array structure even for single items
                    $items = isset($data['chkData']['cd'][0]) ? $data['chkData']['cd'] : [$data['chkData']['cd']];

                    foreach ($items as $cd) {
                        $hostName = $cd['name'];
                        $isAvailable = (bool) $cd['@name']['avail'];
                        $reason = $cd['reason'] ?? null;

                        $results[$hostName] = (object) [
                            'name' => $hostName,
                            'available' => $isAvailable,
                            'reason' => $reason,
                        ];
                    }
                }

                return response()->json(['results' => $results]);
            } else {
                $result = $response ? $response->results()[0] : null;

                return response()->json([
                    'error' => 'Host check failed',
                    'code' => $result ? $result->code() : 'unknown',
                    'message' => $result ? $result->message() : 'No response',
                ], 400);
            }
        } catch (Exception $e) {
            Log::error('Host availability check failed: '.$e->getMessage());

            return response()->json(['error' => 'Failed to check host availability: '.$e->getMessage()], 500);
        }
    }

    /**
     * Create a new host
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'host' => 'required|string|max:255',
            'addresses' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            // First check if the host is available
            $checkFrame = $this->eppService->checkHosts([$request->input('host')]);
            $checkResponse = $this->eppService->getClient()->request($checkFrame);

            if ($checkResponse && $checkResponse->success()) {
                $data = $checkResponse->data();
                if (! empty($data) && isset($data['chkData']['cd'])) {
                    $cd = $data['chkData']['cd'];
                    $isAvailable = (bool) $cd['@name']['avail'];

                    if (! $isAvailable) {
                        return response()->json([
                            'error' => 'Host is not available',
                            'reason' => $cd['reason'] ?? 'Unknown reason',
                        ], 400);
                    }
                }
            }

            $frame = $this->eppService->createHost(
                $request->input('host'),
                $request->input('addresses', [])
            );

            $response = $this->eppService->getClient()->request($frame);

            if ($response && $response->success()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Host created successfully',
                    'data' => $response->data(),
                ]);
            } else {
                $result = $response ? $response->results()[0] : null;

                return response()->json([
                    'error' => 'Failed to create host',
                    'code' => $result ? $result->code() : 'unknown',
                    'message' => $result ? $result->message() : 'No response',
                ], 400);
            }
        } catch (Exception $e) {
            Log::error('Host creation failed: '.$e->getMessage());

            return response()->json(['error' => 'Failed to create host: '.$e->getMessage()], 500);
        }
    }
}
