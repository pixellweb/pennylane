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


    public static function fromIpsum(Client $client): self
    {
        return self::validateAndCreate([
            'name' => $client->nom,
            'vat_number' => $client->vat_numero,
            'reg_no' => $client->siren,
            'phone' => $client->telephone,
            'billing_address' => BillingAdressData::fromIpsum($client),
            'emails' => [$client->email],
            'external_reference' => $client->code
        ]);
    }

}
