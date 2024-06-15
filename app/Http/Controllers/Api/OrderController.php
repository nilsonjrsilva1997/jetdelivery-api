<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('customer', 'orderStatus', 'deliveryAddress', 'paymentMethod', 'deliveryPerson')->get();
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            // 'restaurant_id' => 'required|exists:restaurants,id',
            'order_status_id' => 'required|exists:order_statuses,id',
            'delivery_address_id' => 'required|exists:addresses,id',
            'delivery_date' => 'required|date',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'total_amount' => 'required|numeric|min:0',
            'delivery_fee' => 'nullable|numeric|min:0',
            'company_fee' => 'nullable|numeric|min:0',
        ]);

        $order = Order::create($request->all());

        return response()->json($order, 201);
    }

    public function show(Order $order)
    {
        return response()->json($order::with('customer', 'orderStatus', 'deliveryAddress', 'paymentMethod', 'deliveryPerson')->first());
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'order_status_id' => 'exists:order_statuses,id',
            'delivery_address_id' => 'exists:addresses,id',
            'delivery_date' => 'date',
            'payment_method_id' => 'exists:payment_methods,id',
            'total_amount' => 'numeric|min:0',
            'delivery_fee' => 'numeric|min:0',
            'company_fee' => 'numeric|min:0',
        ]);

        $order->update($request->all());

        return response()->json($order, 200);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(null, 204);
    }
}
