<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\GeocodeAddressJob;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Address::all();
        return response()->json($addresses);
    }

    public function store(Request $request)
    {
        $request->validate([
            'street_address' => 'required|string|max:255',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|size:2', // Aqui 'size:2' garante que o estado tenha exatamente 2 caracteres
            'postal_code' => 'required|string|max:20|regex:/^\d{5}-\d{3}$/',
            'number' => 'required|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $address = Address::create($request->all());

        // Despacha o job para geocodificar o endereço
        GeocodeAddressJob::dispatch($address);

        return response()->json($address, 201);
    }

    public function show(Address $address)
    {
        return response()->json($address);
    }

    public function update(Request $request, Address $address)
    {
        $request->validate([
            'street_address' => 'string|max:255',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'string|max:100',
            'city' => 'string|max:100',
            'state' => 'string|size:2', // Aqui 'size:2' garante que o estado tenha exatamente 2 caracteres
            'postal_code' => 'string|max:20|regex:/^\d{5}-\d{3}$/',
            'number' => 'string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $address->update($request->all());

        // Despacha o job para geocodificar o endereço
        GeocodeAddressJob::dispatch($address);

        return response()->json($address, 200);
    }

    public function destroy(Address $address)
    {
        $address->delete();

        return response()->json(null, 204);
    }
}
