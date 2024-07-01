<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

class AuthController extends Controller
{
    use HasApiTokens;

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('LaravelAuthApp')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function loginRestaurant(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Verifique se o usuário está associado a pelo menos um restaurante
            if ($user->restaurants()->exists()) {
                $token = $user->createToken('LaravelAuthApp')->accessToken;
                $user->restaurants;
                return response()->json(['user' => $user, 'token' => $token], 201);
            } else {
                return response()->json(['error' => 'User not associated with any restaurant'], 403);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function loginDeliveryPeople(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Verifique se o usuário está associado a pelo menos um restaurante
            if ($user->delivery_peoples()->exists()) {
                $token = $user->createToken('LaravelAuthApp')->accessToken;
                $user->delivery_peoples;
                return response()->json(['user' => $user, 'token' => $token], 201);
            } else {
                return response()->json(['error' => 'User not associated with any delivery peoples'], 403);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function registerRestaurant(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $restaurant = \App\Models\Restaurant::find($request->restaurant_id);
        $restaurant->users()->attach($user->id);

        $token = $user->createToken('LaravelAuthApp')->accessToken;

        return response()->json(['token' => $token, 'restaurant' => $restaurant, 'user' => $user], 201);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('LaravelAuthApp')->accessToken;

        return response()->json(['token' => $token], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
