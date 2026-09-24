<?php

namespace PixellWeb\Pennylane\app\Data\Responses;

use Carbon\Carbon;
use Ipsum\Reservation\app\Enum\FactureType;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Optional;


class InvoiceData extends Data
{
    public function __construct(
        public int $id,
        public string $invoice_number,
        public string $amount,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $date,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $deadline,
        public string $public_file_url,
        public string $purchase_order_reference,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s.uP')]
        public Carbon $created_at,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s.uP')]
        public Carbon $updated_at,

    ) {
    }


    public function toIpsum(FactureType $factureType = FactureType::ADDITIONNEL): ?array
    {
        // Ne pas prendre en compte les factures sans réfèrence de commande renseignée
        if (!Reservation::where('reference', $this->purchase_order_reference)->exists()) {
            return null;
        }

        return [
            'numero' => $this->invoice_number,
            'type' => $factureType,
            'provider' => 'pennylane',
            'provider_reference' => $this->id,
            'reservation_id' => $this->purchase_order_reference,
        ];
        
    }


    
}
