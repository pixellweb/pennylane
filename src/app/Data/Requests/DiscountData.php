<?php

namespace PixellWeb\Pennylane\app\Data\Requests;

use Ipsum\Reservation\app\Models\Client;
use Ipsum\Reservation\app\Models\Reservation\Facture;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;


class DiscountData extends Data
{
    public function __construct(
        public string $type,
        public string $value,
    ) {
    }


    public static function fromIpsum(float $remise): array
    {
        return [
            'type' => 'absolute',
            'value' => (string) $remise,
        ];
    }
}
