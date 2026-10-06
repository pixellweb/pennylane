<?php

namespace PixellWeb\Pennylane\app\Data\Responses;

use Ipsum\Reservation\app\Models\Prestation\Tarification;
use Ipsum\Reservation\app\Models\Prestation\Taxe;
use Ipsum\Reservation\app\Models\Prestation\Type;
use PixellWeb\Pennylane\app\Enums\Substance;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;


class ProductData extends Data
{
    public function __construct(
        public int $id,
        public string $label,
        public string $description,
        public string $price_before_tax,
        public string $vat_rate,
        public string $price,
        public string $currency,
        public string $reference,
        #[WithCast(EnumCast::class, type: Substance::class)]
        public Substance $substance,
        public string $external_reference,

    ) {
    }

    public function toIpsum(): array
    {
        $taux = explode('_', $this->vat_rate)[1] / 10;
        $taxe = Taxe::where('taux', $taux)->firstOrFail();

        return [
            'reference_externe' => $this->id,
            'nom' => $this->label,
            'taxe_id' => $taxe->id,
            'montant' => $this->price,
            'type_id' => Type::RESTITUTION_ID,
            'tarification_id' => Tarification::FORFAIT_ID,
        ];

    }
}
