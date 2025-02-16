<?php

namespace App\Http\Controllers;

use App\Services\Epp\EppService;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class TestController extends Controller
{
    protected $eppService;

    protected $contactIds = ['sh8013', 'sah8013', '8013sah'];

    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    /**
     * Check contact availability and return XML response
     *
     * @return \Illuminate\Http\Response
     */
    public function check()
    {
        try {
            $response = $this->eppService->checkContacts($this->contactIds);

            return response($this->formatXmlResponse($response))
                ->header('Content-Type', 'application/xml');

        } catch (\Exception $e) {
            Log::error('Contact check failed', [
                'error' => $e->getMessage(),
                'contact_ids' => $this->contactIds,
                'trace' => $e->getTraceAsString(),
            ]);

            return response($this->formatXmlError($e->getMessage()))
                ->header('Content-Type', 'application/xml');
        }
    }

    /**
     * Format successful response as XML
     *
     * @param  mixed  $response
     * @return string
     */
    private function formatXmlResponse($response)
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="no"?><epp/>');
        $xml->addAttribute('xmlns', 'urn:ietf:params:xml:ns:epp-1.0');

        $response = $xml->addChild('response');
        $result = $response->addChild('result');
        $result->addAttribute('code', '1000');
        $result->addChild('msg', 'Command completed successfully');

        $resData = $response->addChild('resData');
        $chkData = $resData->addChild('contact:chkData');
        $chkData->addAttribute('xmlns:contact', 'urn:ietf:params:xml:ns:contact-1.0');

        if ($response && method_exists($response, 'toArray')) {
            $responseData = $response->toArray();

            foreach ($responseData['checks'] ?? [] as $check) {
                $cd = $chkData->addChild('contact:cd');
                $id = $cd->addChild('contact:id', $check['id']);
                $id->addAttribute('avail', $check['available'] ? '1' : '0');

                if (! empty($check['reason'])) {
                    $cd->addChild('contact:reason', $check['reason']);
                }
            }
        }

        $trID = $response->addChild('trID');
        $trID->addChild('clTRID', 'ABC-12345');
        $trID->addChild('svTRID', 'SERVER-'.time());

        return $xml->asXML();
    }

    /**
     * Format error response as XML
     *
     * @param  string  $errorMessage
     * @return string
     */
    private function formatXmlError($errorMessage)
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="no"?><epp/>');
        $xml->addAttribute('xmlns', 'urn:ietf:params:xml:ns:epp-1.0');

        $response = $xml->addChild('response');
        $result = $response->addChild('result');
        $result->addAttribute('code', '2400'); // Command failed
        $result->addChild('msg', 'Command failed');

        $extValue = $result->addChild('extValue');
        $value = $extValue->addChild('value', 'Error processing command');
        $reason = $extValue->addChild('reason', $errorMessage);

        $trID = $response->addChild('trID');
        $trID->addChild('clTRID', 'ABC-12345');
        $trID->addChild('svTRID', 'SERVER-'.time());

        return $xml->asXML();
    }
}
