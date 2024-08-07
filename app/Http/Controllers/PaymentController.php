<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CieloService;

class PaymentController extends Controller
{
    protected $cieloService;

    public function __construct(CieloService $cieloService)
    {
        $this->cieloService = $cieloService;
    }

    public function processCreditCardPayment(Request $request)
    {
        $paymentData = [
            'MerchantOrderId' => '2021001',
            'Customer' => [
                'Name' => 'Comprador Teste'
            ],
            'Payment' => [
                'Type' => 'CreditCard',
                'Amount' => 15700,
                'Installments' => 1,
                'SoftDescriptor' => 'Teste',
                'CreditCard' => [
                    'CardNumber' => '1234123412341231',
                    'Holder' => 'Comprador T',
                    'ExpirationDate' => '12/2030',
                    'SecurityCode' => '123',
                    'Brand' => 'Visa'
                ]
            ]
        ];

        $response = $this->cieloService->createCreditCardPayment($paymentData);

        return response()->json($response);
    }
}