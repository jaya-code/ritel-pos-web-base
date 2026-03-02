<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MidtransService
{
    protected $serverKey;

    protected $isProduction;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key');
        $this->isProduction = config('services.midtrans.is_production');
    }

    public function getBaseUrl()
    {
        return $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    public function getSnapToken($params)
    {
        // Add basic auth header with Server Key
        // Midtrans requires ServerKey + ":" encoded in Base64
        // Http::withBasicAuth handles this automatically.

        $response = Http::withBasicAuth($this->serverKey, '')
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post($this->getBaseUrl(), $params);

        if ($response->successful()) {
            return $response->json()['token'];
        }

        throw new \Exception('Midtrans Error: '.$response->body());
    }
}
