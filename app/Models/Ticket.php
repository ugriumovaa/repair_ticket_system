<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name',
        'phone',
        'address',
        'problem_text',
        'status',
        'assigned_to',
    ];

    protected $casts = [
        'status' => TicketStatus::class,
    ];

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeByStatus(Builder $query, ?TicketStatus $status): Builder
    {
        return $status
            ? $query->where('status', $status->value)
            : $query;
    }

    public function scopeByAssignedTechnician(Builder $query, ?int $technicianId): Builder
    {
        return $technicianId
            ? $query->where('assigned_to', $technicianId)
            : $query;
    }
}
