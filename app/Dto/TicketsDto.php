<?php

namespace App\Dto;

use Spatie\LaravelData\Data;

class TicketsDto extends Data
{
    public function __construct(
        public int $id,
        public string $client_name,
        public string $phone,
        public string $address,
        public ?string $problem_text,
        public string $status,
        public ?array $assigned_to,
        public ?string $created_at,
    ) {}
}
