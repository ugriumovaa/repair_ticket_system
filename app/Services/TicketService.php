<?php

namespace App\Services;

use App\Dto\Ticket\TicketCreateDto;
use App\Dto\Ticket\TicketDto;
use App\Dto\Ticket\TicketSearchDto;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;

class TicketService
{
    public function getTickets(TicketSearchDto $ticketsSearchDto)
    {
        $query = Ticket::query()
            ->with(['assignedTo:id,name'])
            ->byStatus($ticketsSearchDto->status)
            ->byAssignedTechnician($ticketsSearchDto->assigned_to)
            ->latest();

        $tickets = TicketDto::collect(
            $query->paginate(
                perPage: $ticketsSearchDto->perPage,
                page: $ticketsSearchDto->page,
            ),
        );

        return $tickets;
    }
    public function createTicket(TicketCreateDto $createDto): void
    {
        Ticket::create([
            ...$createDto->toArray(),
            'status' => TicketStatus::New,
        ]);
    }
    private const TRANSITIONS = [
        TicketStatus::New->value => [TicketStatus::Assigned, TicketStatus::Canceled],
        TicketStatus::Assigned->value => [TicketStatus::InProgress, TicketStatus::Canceled],
        TicketStatus::InProgress->value => [TicketStatus::Done, TicketStatus::Canceled],
        TicketStatus::Done->value => [],
        TicketStatus::Canceled->value => [],
    ];
    private const STATUS_ROLES = [
        'dispatcher' => [TicketStatus::Assigned, TicketStatus::Canceled],
        'technician' => [TicketStatus::InProgress, TicketStatus::Done],
    ];
    public function updateTicket(Ticket $ticket, User $actor, ?TicketStatus $newStatus, ?int $assignedTo): Ticket
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

        if ($actor->hasRole('technician') && $ticket->assigned_to !== $actor->id) {
            abort(403, 'Not your ticket');
        }

        $allowedTransitions = self::TRANSITIONS[$from] ?? [];
        if (!in_array($toValue, $allowedTransitions, true)) {
            abort(422, "Invalid status transition: {$from} -> {$toValue}");
        }

        $ticket->status = $to;
    }
}
