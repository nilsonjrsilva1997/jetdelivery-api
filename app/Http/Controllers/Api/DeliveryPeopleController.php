<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPeople;
use Illuminate\Http\Request;

class DeliveryPeopleController extends Controller
{
    public function index()
    {
        $deliveryPeoples = DeliveryPeople::with('address', 'user')->get();
        return response()->json($deliveryPeoples);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'address_id' => 'required|exists:addresses,id',
            'phone' => 'required|string',
            'rg' => 'required|string',
            'cpf' => 'required|string|unique:delivery_peoples,cpf',
            'cnh' => 'required|string',
        ]);

        $deliveryPeople = DeliveryPeople::create($request->all());

        return response()->json($deliveryPeople, 201);
    }

    public function show(DeliveryPeople $deliveryPeople)
    {
        return response()->json($deliveryPeople::with('address', 'user')->first());
    }

    public function update(Request $request, DeliveryPeople $deliveryPeople)
    {
        $request->validate([
            'user_id' => 'exists:users,id',
            'address_id' => 'exists:addresses,id',
            'phone' => 'string',
            'rg' => 'string',
            'cpf' => 'string|unique:delivery_peoples,cpf,' . $deliveryPeople->id,
            'cnh' => 'string',
        ]);

        $deliveryPeople->update($request->all());

        return response()->json($deliveryPeople, 200);
    }

    public function destroy(DeliveryPeople $deliveryPeople)
    {
        $deliveryPeople->delete();
        return response()->json(null, 204);
    }

    /**
     * Atualiza o status online de um entregador.
     */
    public function updateOnlineStatus(Request $request, $deliveryPersonId)
    {
        // Valida a requisição
        $request->validate([
            'online' => 'required|boolean',
        ]);

        // Encontra o entregador pelo ID
        $deliveryPerson = DeliveryPeople::find($deliveryPersonId);

        if (!$deliveryPerson) {
            return response()->json(['message' => 'Entregador não encontrado'], 404);
        }

        // Atualiza o status online
        $deliveryPerson->online = $request->input('online');
        $deliveryPerson->save();

        return response()->json(['message' => 'Status online atualizado com sucesso']);
    }

    /**
     * Verifica o status online de um entregador.
     */
    public function checkOnlineStatus($deliveryPersonId)
    {
        // Encontra o entregador pelo ID
        $deliveryPerson = DeliveryPeople::find($deliveryPersonId);

        if (!$deliveryPerson) {
            return response()->json(['message' => 'Entregador não encontrado'], 404);
        }

        // Retorna o status online
        return response()->json(['online' => $deliveryPerson->online]);
    }
}