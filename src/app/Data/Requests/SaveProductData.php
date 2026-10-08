<?php

namespace PixellWeb\Pennylane\app\Data\Requests;

use Ipsum\Reservation\app\Models\Prestation\Prestation;
use PixellWeb\Pennylane\app\Enums\Substance;
use Spatie\LaravelData\Data;


class SaveProductData extends Data
{
    public function __construct(
        public string $label,
        public string $external_reference,
        public string $price_before_tax,
        public string $vat_rate,
        public ?string $unit,
        public Substance $substance,

    ) {
    }


    public static function fromIpsum(Prestation $prestation): self
    {

        return self::validateAndCreate([
            'label' => $prestation->nom,
            'external_reference' => (string) $prestation->id,
            'price_before_tax' => (string) ($prestation->montant / (1 + ($prestation->taxe->taux / 100))),
            'vat_rate' => $prestation->taxe->taux ? 'FR_'.round($prestation->taxe->taux * 10) : 'exempt',
            'unit' => '',
            'substance' => Substance::SERVICES,
        ]);
    }
}
