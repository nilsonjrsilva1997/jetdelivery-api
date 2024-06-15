<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        return Customer::with('address')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:255',
            'address_id' => 'required|exists:addresses,id',
            'name' => 'required|string|max:255',
        ]);

        return Customer::create($request->all());
    }

    public function show(Customer $customer)
    {
        return $customer->with('address')->first();
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'phone' => 'string|max:255',
            'address_id' => 'exists:addresses,id',
            'name' => 'string|max:255',
        ]);

        $customer->update($request->all());

        return $customer;
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json(null, 204);
    }
}