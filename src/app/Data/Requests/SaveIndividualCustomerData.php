<?php

namespace PixellWeb\Pennylane\app\Data\Requests;

use Ipsum\Reservation\app\Models\Client;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Optional;


class SaveIndividualCustomerData extends Data
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public ?string $phone,
        public BillingAdressData $billing_address,
        public array $emails,
        public ?string $external_reference,
    ) {
    }

    public static function fromIpsum(Reservation $reservation): self
    {
        return self::validateAndCreate([
            'first_name' => $reservation->prenom,
            'last_name' => $reservation->nom,
            'phone' => $reservation->telephone,
            'billing_address' => BillingAdressData::fromIpsum($reservation),
            'emails' => [$reservation->email],
            'external_reference' => $reservation->client ? $reservation->client->code : null
        ]);
    }
}
