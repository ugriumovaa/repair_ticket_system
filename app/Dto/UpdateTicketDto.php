<?php

namespace App\Dto;

use App\Enums\TicketStatus;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class UpdateTicketDto extends Data
{
    public function __construct(
        #[Nullable]
        public ?TicketStatus $status,

        #[Nullable, Exists('users', 'id')]
        public ?int $assigned_to,
    ) {}
}
