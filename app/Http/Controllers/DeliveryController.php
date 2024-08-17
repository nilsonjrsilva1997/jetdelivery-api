<?php

namespace App\Http\Controllers;

use App\Enums\DeliveryStatusEnum;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\DeliveryStatus;
use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        // Obter os IDs dos restaurantes associados ao usuário autenticado
        $restaurantIds = User::where('id', Auth::id())
            ->with('restaurants') // Carregar os restaurantes associados
            ->first()
            ->restaurants
            ->pluck('id'); // Extrair os IDs dos restaurantes

        // Construir a consulta para buscar entregas baseadas em orders e filtradas por restaurant_ids
        $query = Delivery::whereHas('orders', function($query) use ($restaurantIds) {
            $query->whereIn('restaurant_id', $restaurantIds);
        });

        // Aplicar filtros, se fornecidos (descomentados se necessário)
        // $status = $request->query('status'); // Exemplo de filtro por status
        // $fromDate = $request->query('from_date'); // Data inicial
        // $toDate = $request->query('to_date'); // Data final

        // if ($status) {
        //     $query->where('delivery_status_id', $status);
        // }

        // if ($fromDate) {
        //     $query->where('created_at', '>=', $fromDate);
        // }

        // if ($toDate) {
        //     $query->where('created_at', '<=', $toDate);
        // }

        // Ordenar despachos do mais novo para o mais antigo
        $query->orderBy('created_at', 'desc');

        // Buscar despachos com paginação
        $deliveries = $query->with('orders', 'status')
                            ->paginate(5); // Ajuste o número de itens por página conforme necessário

        // Verificar se há despachos encontrados
        if ($deliveries->isEmpty()) {
            return response()->json(['message' => 'Nenhum despacho encontrado'], 404);
        }

        return response()->json($deliveries);
    }

    public function store(Request $request)
    {
        // Validar os dados recebidos
        $validatedData = $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
        ]);

        // Verificar se já existe um despacho com os mesmos order_ids
        $existingDelivery = Delivery::whereHas('orders', function ($query) use ($validatedData) {
            $query->whereIn('orders.id', $validatedData['order_ids']);
        })->exists();

        if ($existingDelivery) {
            return response()->json([
                'message' => 'A delivery with the same orders already exists.',
            ], 400);
        }

        // Criar o despacho (delivery)
        $delivery = Delivery::create([
            'fee' => 20, // calcular o valor da taxa
            'delivery_status_id' => DeliveryStatusEnum::PENDING_COURIER,
        ]);

        // Associar os pedidos ao despacho
        $delivery->orders()->sync($validatedData['order_ids']);

          // Atualizar o status dos pedidos
          Order::whereIn('id', $validatedData['order_ids'])
          ->update(['order_status_id' => OrderStatusEnum::PROCESSING]); // Ou o status apropriado

        return response()->json(['message' => 'Despacho criado com sucesso!'], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $delivery = Delivery::find($id);

        if (!$delivery) {
            return response()->json(['message' => 'Delivery not found'], 404);
        }

        $statusId = $request->input('status_id');
        $status = DeliveryStatus::find($statusId);

        if (!$status) {
            return response()->json(['message' => 'Status not found'], 404);
        }

        $delivery->delivery_status_id = $statusId;
        $delivery->save();

        return response()->json(['message' => 'Delivery status updated successfully'], 200);
    }

    public function show($id)
    {
        try {
            $delivery = Delivery::with('status', 'orders', 'orders.customer', 'orders.deliveryAddress', 'orders.paymentMethod', 'orders.deliveryPerson', 'orders.orderStatus')->findOrFail($id);
            return response()->json($delivery);
        } catch (Exception $e) {
            return response()->json(['error' => 'Delivery not found'], 404);
        }
    }
}
