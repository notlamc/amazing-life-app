<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PayPalService
{
    protected $clientId;
    protected $secret;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = config('paypal.client_id');
        $this->secret = config('paypal.secret');
        $this->baseUrl = config('paypal.mode', 'sandbox') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    // Get PayPal access token
    public function getAccessToken()
    {
        $response = Http::withBasicAuth($this->clientId, $this->secret)
            ->asForm()
            ->post($this->baseUrl . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials'
            ]);

        return $response->json()['access_token'] ?? null;
    }

    // Create a new order
    public function createOrder($amount, $referenceId, $returnUrl, $cancelUrl, $extraParams = [])
    {
        $token = $this->getAccessToken();

        $customData = json_encode([
            'subscription_id' => $extraParams['subscription_id'] ?? null,
            'user_id' => $extraParams['user_id'] ?? null
        ]);

        $response = Http::withToken($token)
            ->post($this->baseUrl . '/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $referenceId,
                        'custom_id'    => $customData, // <--- ONLY THIS FIELD
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => number_format($amount, 2, '.', ''),
                        ],
                        'description' => 'Payment for Order: ' . $referenceId
                    ]
                ],
                'application_context' => [
                    'return_url' => $returnUrl,
                    'cancel_url' => $cancelUrl,
                ]
            ]);

        return $response->json();
    }

    // Capture an approved order
    public function captureOrder($orderId)
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->post($this->baseUrl . "/v2/checkout/orders/{$orderId}/capture", null);  // <-- MUST BE NULL

        return $response->json();
    }
}
