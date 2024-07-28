<?php

namespace App\Services;

use Gerencianet\Gerencianet;
use Illuminate\Support\Facades\Log;

class GerencianetService
{
    private $options;

    public function __construct()
    {
        $this->options = [
            'client_id' => config('gerencianet.client_id'),
            'client_secret' => config('gerencianet.client_secret'),
            'pix_cert' => storage_path(config('gerencianet.pix_cert')),
            'sandbox' => config('gerencianet.sandbox'),
        ];

        // Verificar se o certificado existe
        if (!file_exists($this->options['pix_cert'])) {
            throw new \Exception('Certificate not found: ' . $this->options['pix_cert']);
        }
    }

    public function generatePixQRCode($amount, $txid)
    {
        $gerencianet = new Gerencianet($this->options);

        $body = [
            'calendario' => ['expiracao' => 3600],
            'devedor' => [
                'cpf' => '42282872800',
                'nome' => 'Nome do Devedor'
            ],
            'valor' => [
                'original' => number_format($amount, 2, '.', '')
            ],
            'chave' => 'c5e06e95-045c-4361-a331-298267720d01',
            'solicitacaoPagador' => 'Descrição do pagamento'
        ];

        try {
            $response = $gerencianet->pixCreateImmediateCharge([], $body);

            // Log da resposta para diagnóstico
            Log::info('Gerencianet pixCreateImmediateCharge response: ' . json_encode($response));

            // Verificar se a resposta contém os dados esperados
            if (isset($response['loc']['id'])) {
                $qrcodeResponse = $gerencianet->pixGenerateQRCode(['id' => $response['loc']['id']]);
                return $qrcodeResponse;
            } else {
                throw new \Exception('Unexpected response format: ' . json_encode($response));
            }
        } catch (\Exception $e) {
            // Lidar com erros e exibir a mensagem de erro detalhada
            Log::error('Error generating Pix QR Code: ' . $e->getMessage());
            throw new \Exception('Error generating Pix QR Code: ' . $e->getMessage());
        }
    }

    public function createCreditCardPayment($orderDetails, $cardDetails)
{
    $gerencianet = new Gerencianet($this->options);

    // Passo 1: Criar a cobrança
    $body = [
        'items' => [
            [
                'name' => $orderDetails['item_name'],
                'amount' => 1,
                'value' => $orderDetails['amount']
            ]
        ]
    ];

    try {
        // Criar a cobrança
        $chargeResponse = $gerencianet->createCharge([], $body);

        Log::info('Gerencianet createCharge response: ' . json_encode($chargeResponse));

        if (!isset($chargeResponse['data']['charge_id'])) {
            throw new \Exception('Failed to create charge: ' . json_encode($chargeResponse));
        }

        $chargeId = $chargeResponse['data']['charge_id'];

        // Passo 2: Pagar a cobrança
        $payment = [
            'credit_card' => [
                'installments' => 1,
                'payment_token' => $cardDetails['payment_token'],
                'billing_address' => [
                    'street' => $cardDetails['billing_address']['street'],
                    'number' => $cardDetails['billing_address']['number'],
                    'neighborhood' => $cardDetails['billing_address']['neighborhood'],
                    'zipcode' => $cardDetails['billing_address']['zipcode'],
                    'city' => $cardDetails['billing_address']['city'],
                    'state' => $cardDetails['billing_address']['state']
                ],
                'customer' => [
                    'name' => $cardDetails['customer_name'],
                    'cpf' => $cardDetails['customer_cpf'],
                    'phone_number' => $cardDetails['customer_phone']
                ]
            ]
        ];

        $paymentBody = ['payment' => $payment];

        // Pagar a cobrança
        $paymentResponse = $gerencianet->payCharge(['id' => $chargeId], $paymentBody);

        Log::info('Gerencianet payCharge response: ' . json_encode($paymentResponse));

        return $paymentResponse;
    } catch (\Exception $e) {
        Log::error('Error processing credit card payment: ' . $e->getMessage());
        throw new \Exception('Error processing credit card payment: ' . $e->getMessage());
    }
}
}