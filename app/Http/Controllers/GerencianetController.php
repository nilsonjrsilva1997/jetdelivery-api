<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GerencianetService;

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
}