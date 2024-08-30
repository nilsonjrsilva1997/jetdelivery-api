<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('MAP_BOX_PRIVATE_TOKEN');
        
        if (!$this->apiKey) {
            Log::error('MAP_BOX_PRIVATE_TOKEN is not set in the .env file.');
        }
    }

    public function geocodeAddress($address)
    {
        // Log the address and API key for debugging
        Log::info('Geocoding address: ' . $address);
        Log::info('Using API key: ' . $this->apiKey);

        $client = new Client();

        try {
            $response = $client->get('https://api.mapbox.com/geocoding/v5/mapbox.places/' . urlencode($address) . '.json', [
                'query' => [
                    'access_token' => $this->apiKey,
                ]
            ]);

            $body = json_decode($response->getBody(), true);

            if (isset($body['features'][0]['geometry']['coordinates'])) {
                return [
                    'longitude' => $body['features'][0]['geometry']['coordinates'][0],
                    'latitude' => $body['features'][0]['geometry']['coordinates'][1],
                ];
            }

            return null;
        } catch (RequestException $e) {
            // Log the error response for debugging
            Log::error('Mapbox API request failed: ' . $e->getMessage());
            return null;
        }
    }
}
