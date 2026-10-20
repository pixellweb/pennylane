<?php

namespace PixellWeb\Pennylane\app\Data\Requests;


use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;


class InvoiceLinesData extends Data
{
    public function __construct(
        public int $product_id,
        public ?string $label,
        public ?string $description,
        public string $raw_currency_unit_price,
        public string $vat_rate,
        public int $quantity,
        public DiscountData $discount,
    ) {
    }


    public static function fromIpsum(Prestation $prestation): self
    {
        $prix_unitaire = ($prestation->pivot->montant / (1 + ($prestation->taxe->taux / 100)) + $prestation->pivot->remise) / $prestation->pivot->quantite;

        return self::validateAndCreate([
            'product_id' => $prestation->reference_externe,
            'label' => $prestation->nom,
            'description' => $prestation->pivot->description ?? null,
            'raw_currency_unit_price' => (string) $prix_unitaire,
            'vat_rate' => $prestation->taxe->taux ? 'FR_'.round($prestation->taxe->taux * 10) : 'exempt',
            'quantity' => $prestation->pivot->quantite,
            'discount' => DiscountData::fromIpsum($prestation->pivot->remise),
        ]);
    }
}
