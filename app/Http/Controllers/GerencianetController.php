<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GerencianetService;
use Illuminate\Support\Facades\Log;

class GerencianetController extends Controller
{
    private $gerencianetService;

    public function __construct(GerencianetService $gerencianetService)
    {
        $this->gerencianetService = $gerencianetService;
    }

    public function generatePixQRCode(Request $request)
    {
        $amount = $request->input('amount');
        $txid = $request->input('txid'); // ID da transação

        try {
            $qrcode = $this->gerencianetService->generatePixQRCode($amount, $txid);
            return response()->json($qrcode);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createCreditCardPayment(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string',
            'amount' => 'required|numeric',
            'payment_token' => 'required|string',
            'customer_name' => 'required|string',
            'customer_cpf' => 'required|string',
            'customer_phone' => 'required|string',
            'billing_address.street' => 'required|string',
            'billing_address.number' => 'required|string',
            'billing_address.neighborhood' => 'required|string',
            'billing_address.zipcode' => 'required|string',
            'billing_address.city' => 'required|string',
            'billing_address.state' => 'required|string',
        ]);

        $orderDetails = [
            'item_name' => $request->input('item_name'),
            'amount' => $request->input('amount'),
        ];

        $cardDetails = [
            'payment_token' => $request->input('payment_token'),
            'customer_name' => $request->input('customer_name'),
            'customer_cpf' => $request->input('customer_cpf'),
            'customer_phone' => $request->input('customer_phone'),
            'billing_address' => [
                'street' => $request->input('billing_address.street'),
                'number' => $request->input('billing_address.number'),
                'neighborhood' => $request->input('billing_address.neighborhood'),
                'zipcode' => $request->input('billing_address.zipcode'),
                'city' => $request->input('billing_address.city'),
                'state' => $request->input('billing_address.state'),
            ],
        ];

        try {
            $response = $this->gerencianetService->createCreditCardPayment($orderDetails, $cardDetails);

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Error processing credit card payment: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}