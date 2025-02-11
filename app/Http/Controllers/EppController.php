<?php

namespace App\Http\Controllers;

use App\Services\Epp\EppService;
use Exception;

class EppController extends Controller
{
    private EPPService $eppService;

    public function __construct(EPPService $eppService)
    {
        $this->eppService = $eppService;
    }

    public function connect()
    {
        try {
            $greeting = $this->eppService->connect();

            return response()->json(['greeting' => $greeting]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
