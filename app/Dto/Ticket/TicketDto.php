<?php

namespace App\Dto\Ticket;

use App\Dto\TechnicianDto;
use App\Enums\TicketStatus;
use Spatie\LaravelData\Data;

class TicketDto extends Data
{
    public function __construct(
        public int $id,
        public string $client_name,
        public string $phone,
        public string $address,
        public ?string $problem_text,
        public TicketStatus $status,
        public ?TechnicianDto $assigned_to,
    ) {}

}
