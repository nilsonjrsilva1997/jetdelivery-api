<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Models\IfoodOrder; // Importando o modelo IfoodOrder

class IfoodEventService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function fetchIfoodEvents($ifoodIntegration)
    {
        $url = env('URL_EVENT_POOLING');

        $headers = [
            'types' => 'PLC',
            'groups' => 'DELIVERY',
            'Authorization' => 'Bearer ' . $ifoodIntegration['access_token']
        ];

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => $headers,
            ]);

            $content = json_decode($response->getBody()->getContents(), true);

            foreach ($content as $event) {
                $this->fetchAndSaveOrderDetails($event['orderId'], $ifoodIntegration['access_token'], $ifoodIntegration['restaurant_id']);
            }

        } catch (\Exception $e) {
            // Handle exceptions here
            Log::error($e->getMessage());
        }
    }

    protected function fetchAndSaveOrderDetails($orderId, $accessToken, $restaurantId)
    {
        $url = 'https://merchant-api.ifood.com.br/order/v1.0/orders/' . $orderId;

        $headers = [
            'Authorization' => 'Bearer ' . $accessToken
        ];

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => $headers,
            ]);

            $orderDetails = json_decode($response->getBody()->getContents(), true);

            // Save order details to database
            $ifoodOrder = new IfoodOrder();
            $ifoodOrder->data_ifood = $orderDetails;
            $ifoodOrder->restaurant_id = $restaurantId;
            $ifoodOrder->save();

            Log::debug('Pedido do iFood salvo. Order ID: ' . $orderId);

        } catch (\Exception $e) {
            // Handle exceptions here
            Log::error('Falha ao buscar detalhes do pedido ' . $orderId . ' do iFood: ' . $e->getMessage());
        }
    }
}