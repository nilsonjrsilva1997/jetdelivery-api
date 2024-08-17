<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Obter o usuário autenticado
        $user = User::where('id', Auth::id())->with('wallet')->first();

        // Obter o wallet_id do usuário
        $walletId = $user->wallet->id;

        // Obter a data e hora atual menos 36 horas
        // $cutoffDate = Carbon::now()->subHours(36);

        // Número de itens por página (pode ser ajustado conforme necessário)
        $perPage = $request->get('per_page', 5); // Default to 10 items per page if 'per_page' is not provided

        // Filtrar transações pelo wallet_id do usuário autenticado
        $transactions = Transaction::where('wallet_id', $walletId)
            // ->where('created_at', '>=', $cutoffDate)
            ->orderBy('created_at', 'desc') // Ordenar pelos mais recentes
            ->paginate($perPage); // Use pagination

        return response()->json($transactions);
    }
}
