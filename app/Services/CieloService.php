<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CieloService
{
    protected $client;
    protected $merchantId;
    protected $merchantKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->merchantId = env('CIELO_MERCHANT_ID');
        $this->merchantKey = env('CIELO_MERCHANT_KEY');
        $this->apiUrl = env('CIELO_API_URL_SANDBOX'); // Use API URL sandbox for testing
    }

    public function createCreditCardPayment(array $paymentData)
    {
        $endpoint = $this->apiUrl . '/1/sales/';

        $headers = [
            'Content-Type' => 'application/json',
            'MerchantId' => $this->merchantId,
            'MerchantKey' => $this->merchantKey,
        ];

        try {
            $response = $this->client->post($endpoint, [
                'headers' => $headers,
                'json' => $paymentData,
            ]);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }
}