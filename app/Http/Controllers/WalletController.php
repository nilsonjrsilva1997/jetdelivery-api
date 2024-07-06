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

        return response()->json(['balance' => $user->balance]);
    }

    public function deposit(Request $request)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric'
        ]);

        $user = User::find(Auth::id());

        $user->deposit($validatedData['amount']);

        return response()->json(['balance' => $user->balance]);
    }

    public function withdraw(Request $request)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric'
        ]);

        $user = User::find(Auth::id());

        $user->withdraw($validatedData['amount']);

        return response()->json(['balance' => $user->balance]);
    }
}
