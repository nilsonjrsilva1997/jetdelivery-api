<?php

namespace App\Http\Controllers;

use App\Enums\DeliveryStatusEnum;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\DeliveryStatus;
use App\Models\DeliveryStatusHistory;
use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{

    public function deliveriesForDeliverymans()
    {
        return Delivery::with('orders', 'status')->get();
    }

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

    public function updateStatusDeliveryPeople(Request $request, $id)
    {
        // Validação dos dados recebidos
        $validatedData = $request->validate([
            'delivery_status_id' => 'required|integer|exists:delivery_statuses,id'
        ]);
    
        // Busca a entrega e o ID do delivery_people
        $delivery = Delivery::find($id);
        $deliveryPeopleId = User::where('id', Auth::id())->with('delivery_peoples')->first()->delivery_peoples->id;
    
        // Verifica se a entrega e o delivery_people existem
        if (!$delivery) {
            return response()->json(['message' => 'Delivery not found'], 404);
        }
    
        if (!$deliveryPeopleId) {
            return response()->json(['message' => 'DeliveryPeople not found'], 404);
        }
    
        // Verifica se o status da entrega é realmente diferente
        if ($delivery->delivery_status_id != $validatedData['delivery_status_id']) {
            // Atualiza o status da entrega
            $delivery->delivery_status_id = $validatedData['delivery_status_id'];
            $delivery->delivery_people_id = $deliveryPeopleId;
            $delivery->save();
    
            // Registrar a mudança de status no histórico
            DeliveryStatusHistory::create([
                'delivery_id' => $delivery->id,
                'user_id' => Auth::id(),
                'delivery_status_id' => $validatedData['delivery_status_id'],
                // 'notes' => '',
            ]);
    
            return response()->json(['message' => 'Delivery status updated successfully'], 200);
        }
    
        return response()->json(['message' => 'Status is already set to the requested value'], 200);
    }
    
    public function show($id)
    {
        try {
            $delivery = Delivery::with('status', 'orders', 'orders.customer', 'orders.deliveryAddress', 'orders.paymentMethod', 'orders.deliveryPerson', 'orders.orderStatus', 'orders.restaurant')->findOrFail($id);
            return response()->json($delivery);
        } catch (Exception $e) {
            return response()->json(['error' => 'Delivery not found'], 404);
        }
    }
}
