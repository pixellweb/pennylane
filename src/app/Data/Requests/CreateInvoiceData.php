<?php

namespace PixellWeb\Pennylane\app\Data\Requests;

use Carbon\Carbon;
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
        #[DataCollectionOf(InvoiceLinesData::class)]
        public DataCollection $invoice_lines,
        public string $purchase_order_reference,
    ) {
    }


    public static function fromIpsum(Reservation $reservation, array $dataCollection): self
    {
        return new self(
            now(),
            now()->addMonth(), // TODO
            $reservation->client->custom_fields->pennylane_id ?? $reservation->custom_fields->pennylane_customer_id,
            new DataCollection(InvoiceLinesData::class, $dataCollection),
            $reservation->reference


            // TODO promo
        );
    }

    
}
