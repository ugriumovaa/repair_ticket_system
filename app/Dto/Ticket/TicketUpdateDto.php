<?php

namespace App\Dto\Ticket;

use App\Enums\TicketStatus;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Data;

class TicketUpdateDto extends Data
{
    public function __construct(
        #[Nullable]
        public ?TicketStatus $status,

        #[Nullable, Exists('users', 'id')]
        public ?int $assigned_to,
    ) {}
}
