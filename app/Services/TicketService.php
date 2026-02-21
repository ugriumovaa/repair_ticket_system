<?php

namespace App\Services;

use App\Dto\TicketCreateDto;
use App\Enums\TicketStatus;
use App\Models\Ticket;

class TicketService
{
    public function createTask(TicketCreateDto $createDto): void
    {
        Ticket::create([
            ...$createDto->toArray(),
            'status' => TicketStatus::New,
            'assigned_to' => null
        ]);
    }
    private const TRANSITIONS = [
        'new' => ['assigned', 'canceled'],
        'assigned' => ['in_progress', 'canceled'],
        'in_progress' => ['done', 'canceled'],
        'done' => [],
        'canceled' => [],
    ];

    /**
     * Какие роли могут выставлять КАКИЕ статусы (цели).
     * Dispatcher выставляет assigned/canceled (и только через наши правила).
     * Technician выставляет in_progress/done (и только свои тикеты).
     */
    private const STATUS_ROLES = [
        'dispatcher' => ['assigned', 'canceled'],
        'technician' => ['in_progress', 'done'],
    ];

    public function update(Ticket $ticket, User $actor, ?TicketStatus $newStatus, ?int $assignedTo): Ticket
    {
        if ($assignedTo !== null) {
            if (!$actor->hasRole('dispatcher')) {
                abort(403, 'Only dispatcher can assign technician');
            }
            if ($ticket->status !== TicketStatus::New) {
                abort(422, 'Only NEW ticket can be assigned');
            }

            $ticket->assigned_to = $assignedTo;
            $this->setStatus($ticket, $actor, TicketStatus::Assigned);
        }
        if ($newStatus !== null && $newStatus !== $ticket->status) {
            $this->setStatus($ticket, $actor, $newStatus);
        }

        $ticket->save();

        return $ticket;
    }

    private function setStatus(Ticket $ticket, User $actor, TicketStatus $to): void
    {
        $from = $ticket->status->value;
        $toValue = $to->value;
        $allowedTargets = [];

        foreach (self::STATUS_ROLES as $role => $targets) {
            if ($actor->hasRole($role)) {
                $allowedTargets = array_merge($allowedTargets, $targets);
            }
        }

        if (!in_array($toValue, $allowedTargets, true)) {
            abort(403, 'Role cannot set this status');
        }

        // technician ownership restriction
        if ($actor->hasRole('technician') && $ticket->assigned_to !== $actor->id) {
            abort(403, 'Not your ticket');
        }

        // transition check
        $allowedTransitions = self::TRANSITIONS[$from] ?? [];
        if (!in_array($toValue, $allowedTransitions, true)) {
            abort(422, "Invalid status transition: {$from} -> {$toValue}");
        }

        $ticket->status = $to;
    }

}
