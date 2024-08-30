<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MapboxService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('MAP_BOX_TOKEN'); // Obtém a chave da configuração
    }

    /**
     * Calcula a distância dirigida entre dois pontos usando a API Directions do Mapbox.
     *
     * @param string $origin Coordenada de origem no formato 'longitude,latitude'.
     * @param string $destination Coordenada de destino no formato 'longitude,latitude'.
     * @return float Distância em quilômetros.
     */
    public function getDrivingDistance($coordinates)
    {
        // Crie uma string com as coordenadas no formato adequado para a API do Mapbox
        $coordinatesString = implode(';', $coordinates);

        // Solicite a distância total entre os pontos
        $response = Http::get("https://api.mapbox.com/directions/v5/mapbox/driving/{$coordinatesString}?geometries=geojson&access_token={$this->apiKey}");

        if ($response->failed()) {
            throw new \Exception('Erro ao calcular a distância.');
        }

        $data = $response->json();
        $distance = $data['routes'][0]['distance'] / 1000; // Distância em quilômetros

        return $distance;
    }
}
