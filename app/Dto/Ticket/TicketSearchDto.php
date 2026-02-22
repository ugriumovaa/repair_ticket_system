<?php

namespace App\Dto\Ticket;

use App\Enums\TicketStatus;
use Spatie\LaravelData\Data;

class TicketSearchDto extends Data
{
    public function __construct(
        public ?int $assigned_to = null,
        public ?TicketStatus $status = null,
        public int $page = 1,
        public int $perPage = 10,
    )
    {}
}
