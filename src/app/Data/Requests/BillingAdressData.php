<?php

namespace PixellWeb\Pennylane\app\Data\Requests;

use Ipsum\Reservation\app\Models\Client;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;


class BillingAdressData extends Data
{
    public function __construct(
        public string $address,
        public string $postal_code,
        public string $city,
        public string $country_alpha2,
    ) {
    }


    public static function fromIpsum(Reservation $data): array
    {
        return [
            'address' => $data->adresse,
            'postal_code' => $data->cp,
            'city' => $data->ville,
            'country_alpha2' => $data->pays?->alpha2,
        ];
    }
}
