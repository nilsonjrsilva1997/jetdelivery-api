<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Obter os IDs dos restaurantes associados ao usuário autenticado
        $restaurantIds = User::where('id', Auth::id())
            ->with('restaurants') // Carregar os restaurantes associados
            ->first()
            ->restaurants
            ->pluck('id'); // Extrair os IDs dos restaurantes

        // Obter a data e hora atual menos 36 horas
        $cutoffDate = Carbon::now()->subHours(36);

        // Número de itens por página (pode ser ajustado conforme necessário)
        $perPage = $request->get('per_page', 5); // Default to 5 items per page if 'per_page' is not provided

        // Filtrar pedidos que não estão associados a um delivery, que têm status "Pending", e que foram criados nas últimas 36 horas
        $orders = Order::with('customer', 'orderStatus', 'deliveryAddress', 'paymentMethod', 'deliveryPerson', 'restaurant', 'restaurant.address', 'restaurant.category', 'ifoodOrders')
            ->whereIn('restaurant_id', $restaurantIds) // Filtrar pelos pedidos dos restaurantes associados ao usuário
            ->whereNotIn('order_status_id', [OrderStatusEnum::PROCESSING, OrderStatusEnum::SHIPPED, OrderStatusEnum::DELIVERED, OrderStatusEnum::CANCELLED])
            ->where('created_at', '>=', $cutoffDate)
            ->orderBy('created_at', 'desc') // Ordenar pelos pedidos mais recentes
            ->paginate($perPage); // Use pagination

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'restaurant_id' => 'required|exists:restaurants,id',
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
        return response()->json($order::with('customer', 'orderStatus', 'deliveryAddress', 'paymentMethod', 'deliveryPerson', 'restaurant', 'restaurant.address', 'restaurant.category')->first());
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
