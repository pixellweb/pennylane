<?php

namespace PixellWeb\Pennylane\app\Data\Requests;


use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Ipsum\Reservation\app\Models\Prestation\Type;
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
        public Optional|DiscountData $discount,
    ) {
    }


    public static function fromIpsum(Prestation $prestation): self
    {
        $prix_unitaire = ($prestation->pivot->montant / (1 + ($prestation->taxe->taux / 100)) + $prestation->pivot->remise) / $prestation->pivot->quantite;

        return self::validateAndCreate(array_filter([
            'product_id' => $prestation->reference_externe,
            'label' => null,
            'description' => $prestation->pivot->description ?? null,
            'raw_currency_unit_price' => (string) $prix_unitaire,
            'vat_rate' => $prestation->taxe->taux ? 'FR_'.round($prestation->taxe->taux * 10) : 'exempt', // TODO code dupliqué
            'quantity' => $prestation->pivot->quantite,
            'discount' => $prestation->pivot->remise ? DiscountData::fromIpsum($prestation->pivot->remise) : null,
        ]));
    }
}
