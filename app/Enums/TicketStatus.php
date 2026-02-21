<?php

namespace App\Enums;

enum TicketStatus: string
{
    case New = 'new';
    case Assigned = 'assigned';
    case InProgress = 'in_progress';
    case Done = 'done';
    case Canceled = 'canceled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
