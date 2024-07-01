<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function index()
    {
        return Restaurant::with('address', 'category')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'cpf_cnpj' => 'required|string|max:14',
            'address_id' => 'required|exists:addresses,id',
            'phone' => 'required|string|max:15',
            'fantasy_name' => 'required|string|max:255',
        ]);

        $restaurant = Restaurant::create($request->all());

        return response()->json($restaurant, 201);
    }

    public function show($id)
    {
        $restaurant = Restaurant::with('address', 'category')->where('id', $id)->first();

        if (is_null($restaurant)) {
            return response()->json(['message' => 'Restaurant not found'], 404);
        }

        return response()->json($restaurant);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'exists:categories,id',
            'cpf_cnpj' => 'string|max:14',
            'address_id' => 'exists:addresses,id',
            'phone' => 'string|max:15',
            'fantasy_name' => 'string|max:255',
        ]);

        $restaurant = Restaurant::find($id);

        if (is_null($restaurant)) {
            return response()->json(['message' => 'Restaurant not found'], 404);
        }

        $restaurant->update($request->all());

        return response()->json($restaurant);
    }

    public function destroy($id)
    {
        $restaurant = Restaurant::find($id);

        if (is_null($restaurant)) {
            return response()->json(['message' => 'Restaurant not found'], 404);
        }

        $restaurant->delete();

        return response()->json(null, 204);
    }
}
