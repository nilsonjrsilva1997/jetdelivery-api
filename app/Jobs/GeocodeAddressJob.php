<?php

namespace App\Jobs;

use App\Models\Address;
use App\Services\GeocodingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use function App\Helpers\formatAddress;

class GeocodeAddressJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $address;

    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    public function handle(GeocodingService $geocodingService)
    {
        // Busca a latitude e longitude utilizando o GeocodingService
        $coordinates = $geocodingService->geocodeAddress(formatAddress($this->address));

        if ($coordinates) {
            $this->address->latitude = $coordinates['lat'];
            $this->address->longitude = $coordinates['lng'];
            $this->address->save();
        }
    }
}