<?php

namespace PixellWeb\Pennylane\app\Data\Requests;

use Ipsum\Reservation\app\Models\Reservation\Facture;
use Spatie\LaravelData\Data;


class SaveIndividualCustomerData extends Data
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public ?string $phone,
        public BillingAdressData $billing_address,
        public array $emails,
        public string $external_reference,
    ) {
    }

    public static function fromIpsum(Facture $facture): self
    {
        return self::validateAndCreate([
            'first_name' => $facture->reservation->prenom,
            'last_name' => $facture->reservation->nom,
            'phone' => $facture->reservation->telephone,
            'billing_address' => BillingAdressData::fromIpsum($facture->reservation),
            'emails' => [$facture->reservation->email],
            'external_reference' => $facture->client->code
        ]);
    }
}
