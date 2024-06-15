<?php

namespace App\Services;

use GuzzleHttp\Client;

class GeocodingService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google_maps.api_key');
    }

    public function geocodeAddress($address)
    {
        $client = new Client();
        $response = $client->get('https://maps.googleapis.com/maps/api/geocode/json', [
            'query' => [
                'address' => $address,
                'key' => $this->apiKey,
            ]
        ]);

        $body = json_decode($response->getBody(), true);

        if (isset($body['results'][0]['geometry']['location'])) {
            return $body['results'][0]['geometry']['location'];
        }

        return null;
    }
}