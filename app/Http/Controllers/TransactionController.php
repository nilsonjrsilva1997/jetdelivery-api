<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Verifique se há parâmetros de consulta para filtrar as transações
        $query = Transaction::query();

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->has('confirmed')) {
            $query->where('confirmed', $request->input('confirmed'));
        }

        // Adicione mais filtros conforme necessário

        // Obtenha as transações paginadas
        $transactions = $query->paginate(10);

        return response()->json($transactions);
    }
}
