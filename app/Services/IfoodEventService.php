<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Customer;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Models\IfoodOrder; // Importando o modelo IfoodOrder
use App\Models\Order;
use Carbon\Carbon;

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

            // Create or update customer
            $customerData = [
                'phone' => $orderDetails['customer']['phone']['number'],
                'address_id' => $this->createOrUpdateAddress($orderDetails['delivery']['deliveryAddress']),
                'name' => $orderDetails['customer']['name'],
            ];

            $customer = Customer::updateOrCreate(
                ['phone' => $customerData['phone']],
                $customerData
            );

            // Create or update order
            $orderData = [
                'customer_id' => $customer->id,
                'restaurant_id' => $restaurantId,
                'order_status_id' => $this->getOrderStatusId($orderDetails['orderStatus'] ?? 'UNKNOWN'),
                'delivery_address_id' => $customerData['address_id'],
                'delivery_date' => Carbon::parse($orderDetails['delivery']['deliveryDateTime'])->format('Y-m-d H:i:s'),
                'payment_method_id' => $this->getPaymentMethodId($orderDetails['payments']['methods'][0]['method'] ?? 'UNKNOWN'),
                'total_amount' => $orderDetails['total']['orderAmount'],
                'delivery_fee' => $orderDetails['total']['deliveryFee'],
                'company_fee' => $orderDetails['total']['additionalFees'],
            ];

            $order = Order::updateOrCreate(
                ['id' => $orderId],
                $orderData
            );

            Log::debug($order);

            // Save order details to database
            $ifoodOrder = new IfoodOrder();
            $ifoodOrder->data_ifood = $orderDetails;
            $ifoodOrder->restaurant_id = $restaurantId;
            $ifoodOrder->order_id = $order->id;
            $ifoodOrder->save();

            Log::debug('Pedido do iFood salvo. Order ID: ' . $orderId);

        } catch (\Exception $e) {
            // Handle exceptions here
            Log::error('Falha ao buscar detalhes do pedido ' . $orderId . ' do iFood: ' . $e->getMessage());
        }
    }

    protected function createOrUpdateAddress($addressDetails)
    {
        // Verifique se o endereço já existe e atualize ou crie um novo
        $address = Address::firstOrCreate([
            'street_address' => $addressDetails['streetName'],
            'number' => $addressDetails['streetNumber'],
            'postal_code' => $addressDetails['postalCode']
        ], [
            'complement' => $addressDetails['complement'] ?? '',
            'neighborhood' => $addressDetails['neighborhood'],
            'city' => $addressDetails['city'],
            'state' => $addressDetails['state'],
            'latitude' => $addressDetails['latitude'] ?? null,
            'longitude' => $addressDetails['longitude'] ?? null,
        ]);

        return $address->id;
    }

    protected function createOrUpdateCustomer($customerDetails, $addressId)
    {
        // Verifique se o cliente já existe e atualize ou crie um novo
        $customer = Customer::firstOrCreate([
            'phone' => $customerDetails['phone'],
            'name' => $customerDetails['name'],
        ], [
            'address_id' => $addressId,
        ]);

        return $customer->id;
    }

    protected function mapOrderStatus($status)
    {
        // Mapeie o status do pedido do iFood para o status da sua plataforma
        // Exemplo:
        return $status === 'completed' ? 1 : 2; // Exemplo de mapeamento
    }

    protected function mapPaymentMethod($paymentMethod)
    {
        // Mapeie o método de pagamento do iFood para o método de pagamento da sua plataforma
        // Exemplo:
        return 1; // Substitua pelo ID real
    }

    protected function getPaymentMethodId($paymentMethod)
    {
        // Implement logic to map iFood payment method to your system's payment method ID
        // Example placeholder logic:
        return 1; // Default payment method ID
    }

    protected function getOrderStatusId($status)
    {
        // Implement logic to map iFood order status to your system's order status ID
        // Example placeholder logic:
        return 1; // Default status ID
    }

}