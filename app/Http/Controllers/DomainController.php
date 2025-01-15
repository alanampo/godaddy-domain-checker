<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoDaddyService;

class DomainController extends Controller
{
    protected $goDaddyService;

    public function __construct(GoDaddyService $goDaddyService)
    {
        $this->goDaddyService = $goDaddyService;
    }

    public function check(Request $request)
    {
        $request->validate(['domain' => 'required|regex:/^[a-zA-Z0-9-]+\.[a-zA-Z]{2,}$/']);

        $result = $this->goDaddyService->checkDomainAvailability($request->input('domain'));
        $whoisInfo = NULL;
        if (!$result['available']) {
            $whoisInfo = $this->goDaddyService->getWhoisInfo($request->input('domain'));
        }
        $dnsRecords = NULL;
        if (!$result['available']) {
            $dnsRecords = $this->goDaddyService->checkDnsRecords($request->input('domain'));
        }
        return view('domain-check', compact('result', 'whoisInfo', 'dnsRecords'));
    }
}
