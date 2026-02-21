<?php

namespace App\Dto;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Symfony\Contracts\Service\Attribute\Required;

class TicketCreateDto extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $client_name,

        #[Required, StringType, Max(50)]
        public string $phone,

        #[Required, StringType, Max(255)]
        public string $address,

        #[Required, StringType, Max(5000)]
        public string $problem_text,
    ) {}
}
