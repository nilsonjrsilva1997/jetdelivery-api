<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use MercadoPago\SDK;
use MercadoPago\Payment;
use MercadoPago\CardToken;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Configurações do Mercado Pago
        SDK::setAccessToken(env('MERCADO_PAGO_ACCESS_TOKEN'));
    }

    public function processPayment(Request $request)
    {
        // Valida os dados recebidos
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'token' => 'required|string',
            'description' => 'required|string',
            'installments' => 'required|integer|min:1',
            'payment_method_id' => 'required|string',
            'payer_email' => 'required|email',
        ]);

        // Configura os dados do pagamento
        $paymentData = [
            'transaction_amount' => $request->input('amount'),
            'token' => $request->input('token'),
            'description' => 'Caneta esfereográfica bic',
            'installments' => $request->input('installments'),
            'payment_method_id' => $request->input('payment_method_id'),
            'payer' => [
                'email' => $request->input('payer_email'),
                'identification' => [
                    'number' => '42282872800',  // Use dados válidos para o CPF
                    'type' => 'CPF'
                ]
            ],
        ];

        // Gera um UUID para o X-Idempotency-Key
        $idempotencyKey = (string) \Str::uuid();

        try {
            // Faz a chamada direta para a API do MercadoPago
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('MERCADO_PAGO_ACCESS_TOKEN'),
                'X-Idempotency-Key' => $idempotencyKey,
            ])->post('https://api.mercadopago.com/v1/payments', $paymentData);

            // Decodifica a resposta
            $responseData = $response->json();

            // Verifica o status do pagamento
            if ($response->successful()) {
                if ($responseData['status'] === 'approved') {
                    return response()->json(['message' => 'Pagamento aprovado', 'payment' => $responseData], 200);
                } else {
                    return response()->json(['message' => 'Pagamento não aprovado', 'payment' => $responseData], 400);
                }
            } else {
                // Caso a resposta não seja bem-sucedida, retorna o erro
                Log::error('Erro ao processar pagamento', ['response' => $responseData]);
                return response()->json(['message' => 'Erro ao processar pagamento', 'error' => $responseData], 400);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Erro ao processar pagamento', 'error' => $e->getMessage()], 500);
        }
    }

    public function generateToken(Request $request)
    {
        // Valida os dados recebidos
        $validatedData = $request->validate([
            'cardNumber' => 'required|string',
            'cardholderName' => 'required|string',
            'expirationDate' => 'required|string',
            'securityCode' => 'required|string',
            'paymentMethodId' => 'required|string',
        ]);

        try {
            // Cria o token do cartão
            $cardToken = new CardToken();
            $cardToken->card_number = $validatedData['cardNumber'];
            $cardToken->cardholder_name = $validatedData['cardholderName'];
            $cardToken->expiration_date = $validatedData['expirationDate'];
            $cardToken->security_code = $validatedData['securityCode'];
            $cardToken->payment_method_id = $validatedData['paymentMethodId'];

            $tokenResponse = $cardToken->save();

            return response()->json(['token' => $tokenResponse], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao gerar token.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}