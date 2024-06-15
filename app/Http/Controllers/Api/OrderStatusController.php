<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public function index()
    {
        $orderStatuses = OrderStatus::all();
        return response()->json($orderStatuses, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $orderStatus = OrderStatus::create([
            'name' => $request->name,
        ]);

        return response()->json($orderStatus, 201);
    }

    public function show(OrderStatus $orderStatus)
    {
        return response()->json($orderStatus, 200);
    }

    public function update(Request $request, OrderStatus $orderStatus)
    {
        $request->validate([
            'name' => 'string|max:255',
        ]);

        $orderStatus->update([
            'name' => $request->name,
        ]);

        return response()->json($orderStatus, 200);
    }

    public function destroy(OrderStatus $orderStatus)
    {
        $orderStatus->delete();
        return response()->json(null, 204);
    }
}