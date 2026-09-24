<?php

namespace PixellWeb\Pennylane\app\Data\Requests;

use Ipsum\Reservation\app\Models\Client;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Optional;


class SaveCompagnyCustomerData extends Data
{

    public function __construct(
        public string $name,
        public ?string $vat_number,
        public ?string $reg_no,
        public ?string $phone,
        public BillingAdressData $billing_address,
        public ?array $emails,
        public ?string $external_reference,
    ) {
    }


    public static function fromIpsum(Reservation $reservation): self
    {
        return self::validateAndCreate([
            'name' => 'ff',//$reservation->entreprise, // TODO champ inexistant
            'vat_number' => $reservation->numero_tva, // TODO champ inexistant
            'reg_no' => $reservation->siren, // TODO  champ inexistant
            'phone' => $reservation->telephone,
            'billing_address' => BillingAdressData::fromIpsum($reservation),
            'emails' => [$reservation->email],
            'external_reference' => $reservation->client ? $reservation->client->code : null
        ]);
    }

}
