<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function getBalance()
    {
        $user = User::find(Auth::id());

        return response()->json(['balance' => $user->balanceFloat]);
    }

    public function deposit(Request $request)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric'
        ]);

        $user = User::find(Auth::id());

        $user->depositFloat($validatedData['amount'], ['description' => 'payment of taxes']);

        return response()->json(['balance' => $user->balance]);
    }

    public function withdraw(Request $request)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric'
        ]);

        $user = User::find(Auth::id());

        

        $user->withdrawFloat($validatedData['amount'], ['description' => 'payment of taxes']);

        return response()->json(['balance' => $user->balance]);
    }
}
