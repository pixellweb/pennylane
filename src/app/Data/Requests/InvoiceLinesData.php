<?php

namespace PixellWeb\Pennylane\app\Data\Requests;


use Ipsum\Reservation\app\Models\Reservation\Casts\Prestation;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;


class InvoiceLinesData extends Data
{
    public function __construct(
        public int $product_id,
        public Optional|string $label,
        public Optional|string $description,
        public string $raw_currency_unit_price,
        public string $vat_rate,
        public int $quantity,
    ) {
    }


    public static function fromIpsum(Reservation $reservation, Prestation $prestation): self
    {
        return new self(
            '97020051456', // TODO
            Optional::create(),
            'Catégorie '.$reservation->categorie_nom . ' du '.$reservation->debut_at->format('d/m/Y').' au '.$reservation->fin_at->format('d/m/Y'),
            $reservation->montant_base, // TODO calculer en ht
            'FR_85', // TODO tva prévoir un custom field dans prestation
            1,
        );
    }
}
