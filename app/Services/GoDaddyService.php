<?php
namespace App\Services;

use GuzzleHttp\Client;
use Iodev\Whois\Factory;
use Illuminate\Support\Facades\Http;
class GoDaddyService
{
    protected $client;
    protected $apiKey;
    protected $apiSecret;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.ote-godaddy.com/v1/',
        ]);

        $this->apiKey = env('GODADDY_API_KEY');
        $this->apiSecret = env('GODADDY_API_SECRET');
    }

    public function checkDomainAvailability(string $domain): array
    {
        try {
            $response = $this->client->get("domains/available", [
                'query' => ['domain' => $domain],
                'headers' => [
                    'Authorization' => 'sso-key ' . $this->apiKey . ':' . $this->apiSecret,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getWhoisInfo($domain)
    {
        $whois = Factory::get()->createWhois();
        try {
            $info = $whois->loadDomainInfo($domain);
            return [
                'registrar' => $info->registrar,
                'creationDate' => date('d/m/Y', $info->creationDate),
                'expirationDate' => date('d/m/Y', $info->expirationDate),
            ];
        } catch (\Exception $e) {
            return ['error' => 'Unable to fetch WHOIS data'];
        }
    }

    public function checkDnsRecords($domain)
    {
        return [
            'A' => dns_get_record($domain, DNS_A),
            'MX' => dns_get_record($domain, DNS_MX),
            'CNAME' => dns_get_record($domain, DNS_CNAME),
        ];
    }

    public function suggestAlternateDomains($domain)
    {
        $apiKey = env('GODADDY_API_KEY');
        $apiSecret = env('GODADDY_API_SECRET');
        $url = "https://api.ote-godaddy.com/v1/domains/suggest";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'sso-key ' . $apiKey . ':' . $apiSecret,
            ])->get($url, [
                        'query' => $domain,
                    ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                return ['error' => 'Si è verificato un errore: ' . $response->body()];
            }
        } catch (\Exception $e) {
            return ['error' => 'Errore: ' . $e->getMessage()];
        }
    }
}
