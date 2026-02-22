<?php

namespace App\Dto\Ticket;

use App\Enums\TicketStatus;
use Spatie\LaravelData\Data;

class TicketUpdateDto extends Data
{
    public function __construct(
        public int $id,
        public int $userId,
        public ?TicketStatus $status = null,
        public ?int $assigned_to = null,
    ) {}
}
