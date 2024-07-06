<?php

namespace App\Http\Controllers;

use App\Models\IfoodIntegration;
use App\Models\IfoodOrder;
use App\Models\Restaurant;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IfoodIntegrationController extends Controller
{
    public function getOauthUserCode(Request $request)
    {
        // Verifica se o usuário autenticado possui um restaurante associado
        $restaurant = Restaurant::whereHas('users', function($query) {
            $query->where('user_id', Auth::id());
        })->first();

        if (!$restaurant) {
            return response()->json(['error' => 'Usuário não possui um restaurante associado'], 404);
        }

        // Cria uma instância do Guzzle HTTP client
        $client = new Client();

        // Define a URL para a requisição
        $url = env('URL_IFOOD_USER_CODE');

        // Define os headers e os dados da requisição
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $data = [
            'clientId' => env('IFOOD_CLIENT_ID'),
        ];

        // Faz a requisição POST
        $response = $client->post($url, [
            'headers' => $headers,
            'form_params' => $data,
        ]);

        // Pega o corpo da resposta
        $body = $response->getBody();
        $content = json_decode($body->getContents());
        
        // Dados para encontrar o registro existente
        $attributes = ['restaurant_id' => $restaurant['id']];

        // salvando dados para integração
        $values = [
            'active' => true,
            'restaurant_id' => $restaurant['id'],
            'authorization_code_verifier' => $content->authorizationCodeVerifier,
        ];

        // Cria ou atualiza o integracao com ifood
        IfoodIntegration::updateOrCreate($attributes, $values);

        // Retorna o conteúdo da resposta
        return response()->json($content);
    }

    public function getOauthToken(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|string'
        ]);

        // Verifica se o usuário autenticado possui um restaurante associado
        $restaurant = Restaurant::whereHas('users', function($query) {
            $query->where('user_id', Auth::id());
        })->with('ifood_integration')->first();

        if (!$restaurant) {
            return response()->json(['error' => 'Usuário não possui um restaurante associado/integração'], 404);
        }

        $ifoodIntegration = $restaurant->ifood_integration;

        // Cria uma instância do Guzzle HTTP client
        $client = new Client();

        // Define a URL para a requisição
        $url = env('URL_IFOOD_TOKEN');

        // Define os headers e os dados da requisição
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $data = [
            'clientId' => env('IFOOD_CLIENT_ID'),
            'clientSecret' => env('IFOOD_CLIENT_SECRET'),
            'grantType' => 'authorization_code',
            'authorizationCodeVerifier' => $ifoodIntegration->authorization_code_verifier,
            'authorizationCode' => $validatedData['code'],
        ];

        // Faz a requisição POST
        try {
            $response = $client->post($url, [
                'headers' => $headers,
                'form_params' => $data,
            ]);

            // Pega o corpo da resposta
            $body = $response->getBody();
            $content = json_decode($body->getContents());

             // Atualiza os dados da integração iFood
             $ifoodIntegration->update([
                'access_token' => $content->accessToken,
                'refresh_token' => $content->refreshToken,
            ]);

            // Retorna o conteúdo da resposta da API
            return response()->json($content);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getIntegrationStatus()
    {
        // Verifica se o usuário autenticado possui um restaurante associado
        $restaurant = Restaurant::whereHas('users', function($query) {
            $query->where('user_id', Auth::id());
        })->with('ifood_integration')->first();

        if (!$restaurant) {
            return response()->json(['error' => 'Usuário não possui um restaurante associado/integração'], 404);
        }

        // Obtém os dados da integração com iFood
        $ifoodIntegration = $restaurant->ifood_integration;

        // Verifica se a integração está ativa
        if ($ifoodIntegration && $ifoodIntegration->active == 1) {
            // Verifica a validade dos tokens
            $currentTime = time();

            try {
                // Decodifica o access token
                $accessTokenData = json_decode(base64_decode(explode('.', $ifoodIntegration->access_token)[1]), true);
                if ($accessTokenData['exp'] < $currentTime) {
                    return response()->json(['status' => false], 200);
                }

                // Decodifica o refresh token
                $refreshTokenData = json_decode(base64_decode(explode('.', $ifoodIntegration->refresh_token)[1]), true);
                if ($refreshTokenData['exp'] < $currentTime) {
                    return response()->json(['status' => false], 200);
                }
            } catch (\Exception $e) {
                return response()->json(['status' => false], 500);
            }

            // Se tudo estiver correto
            return response()->json(['status' => true], 200);
        } else {
            return response()->json(['status' => false], 200);
        }
    }

    public function getOrders()
    {
        $orders = IfoodOrder::all();

        return response()->json([
            'orders' => $orders
        ]);
    }
}
