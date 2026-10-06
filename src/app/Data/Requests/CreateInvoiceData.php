<?php

namespace PixellWeb\Pennylane\app\Data\Requests;

use Carbon\Carbon;
use Ipsum\Reservation\app\Models\Reservation\Facture;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;


class CreateInvoiceData extends Data
{
    public function __construct(
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d')]
        public Carbon $date,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d')]
        public Carbon $deadline,
        public int $customer_id,
        public Optional|DiscountData $discount,
        public string $purchase_order_reference,
        public ?bool $draft,
        #[DataCollectionOf(InvoiceLinesData::class)]
        public DataCollection $invoice_lines,
    ) {
    }


    public static function fromIpsum(Facture $facture, bool $brouillon): self
    {

        $dataCollection = [];
        foreach ($facture->produits as $produit) {
            $dataCollection[] = InvoiceLinesData::fromIpsum($produit)->toArray();
        }

        return self::validateAndCreate(array_filter([
            'date' => now(),
            'deadline' => $facture->echeance_at,
            'customer_id' => $facture->client->reference_externe,
            //'discount' => $facture->remise ? DiscountData::fromIpsum($facture) : null, // Remise au niveau des produits car cela pose problème avec le calcul de la tva
            'purchase_order_reference' => $facture->reservation->reference,
            'draft' => $brouillon,
            'invoice_lines' => $dataCollection
        ]));
    }

    
}
