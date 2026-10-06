<?php

namespace PixellWeb\Pennylane\app\Data\Requests;

use Carbon\Carbon;
use Ipsum\Reservation\app\Models\Reservation\Facture;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use PixellWeb\Pennylane\app\Data\Responses\InvoiceData;
use PixellWeb\Pennylane\app\Ressources\Invoice;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;


class UpdateInvoiceData extends Data
{
    public function __construct(
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d')]
        public Carbon $date,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d')]
        public Carbon $deadline,
        public int $customer_id,
        public Optional|DiscountData $discount,
        public array $invoice_lines,
    ) {
    }


    public static function fromIpsum(Facture $facture, Invoice $invoice): self
    {

        // Pour mettre à jour un brouillon de facture, il faut aller chercher les ids des lignes de produit à créer, modifier ou supprimer
        $lines = collect($invoice->listLines($facture->provider_reference));

        $dataCreate = $dataUpdate = [];
        foreach ($facture->produits as $produit) {
            $line = $lines->firstWhere('product.id', $produit->reference_externe);
            if ($line) {
                $dataUpdate[] = InvoiceLinesData::fromIpsum($produit)->toArray() + ['id' => $line['id']];
            } else {
                $dataCreate[] = InvoiceLinesData::fromIpsum($produit)->toArray();
            }
        }

        $refs = $facture->produits->pluck('reference_externe')->toArray();
        $dataDelete = $lines->filter(function ($line) use ($refs) {
            return !in_array($line['product']['id'], $refs, false);
        })->mapWithKeys(function ($line) { return [['id' => $line['id']]]; })->toArray();

        return self::validateAndCreate(array_filter([
            'date' => now(),
            'deadline' => $facture->echeance_at,
            'customer_id' => $facture->client->reference_externe,
            'invoice_lines' => [
                'create' => $dataCreate,
                'update' => $dataUpdate,
                'delete' => $dataDelete
            ]
        ]));
    }

    
}
