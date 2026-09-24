<?php

namespace PixellWeb\Pennylane\app\Data\Responses;

use Spatie\LaravelData\Data;


class CustomerData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $first_name,
        public ?string $last_name,
        public string $phone,
        public ?string $vat_number,
        public ?string $reg_no,
        public array $emails,
        public string $external_reference,

    ) {
    }

    
}
