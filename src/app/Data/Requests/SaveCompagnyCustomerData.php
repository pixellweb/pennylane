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
            'name' => $reservation->entreprise->nom,
            'vat_number' => $reservation->entreprise->vat_numero,
            'reg_no' => $reservation->entreprise->siren,
            'phone' => $reservation->entreprise->telephone,
            'billing_address' => BillingAdressData::fromIpsum($reservation->entreprise),
            'emails' => [$reservation->entreprise->email],
            'external_reference' => $reservation->entreprise->code
        ]);
    }

}
