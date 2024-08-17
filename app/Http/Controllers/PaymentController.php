<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MercadoPago\SDK;
use MercadoPago\Payment;

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
            'payer_email' => 'required|email',
            'payment_method_id' => 'required|string', // Adiciona o método de pagamento aqui
        ]);

        try {
            $payment = new Payment();
            $payment->transaction_amount = $request->input('amount');
            $payment->token = $request->input('token');
            $payment->description = $request->input('description');
            $payment->installments = $request->input('installments');
            $payment->payment_method_id = $request->input('payment_method_id'); // Recebe o método de pagamento
            $payment->payer_email = $request->input('payer_email');
            $payment->save();

            if ($payment->status === 'approved') {
                return response()->json(['message' => 'Pagamento aprovado', 'payment' => $payment], 200);
            } else {
                return response()->json(['message' => 'Pagamento não aprovado', 'payment' => $payment], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao processar pagamento', 'error' => $e->getMessage()], 500);
        }
    }
}
