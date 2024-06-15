<?php

namespace App\Helpers;

use App\Models\Address;

if (! function_exists('formatAddress')) {
    /**
     * Formata um endereço completo.
     *
     * @param Address $address
     * @return string
     */
    function formatAddress(Address $address)
    {
        return "{$address->street_address}, {$address->number}, {$address->complement}, {$address->neighborhood}, {$address->city} - {$address->state}, {$address->postal_code}";
    }
}