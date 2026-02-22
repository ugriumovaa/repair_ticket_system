<?php

namespace App\Services;

use App\Dto\Ticket\TicketCreateDto;
use App\Dto\Ticket\TicketDto;
use App\Dto\Ticket\TicketSearchDto;
use App\Dto\Ticket\TicketUpdateDto;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Pagination\AbstractPaginator;

class TicketService
{
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

    public function getTickets(TicketSearchDto $searchDto): AbstractPaginator
    {
        $query = Ticket::query()
            ->with(['assignedTo:id,name'])
            ->byStatus($searchDto->status)
            ->byAssignedTechnician($searchDto->assigned_to)
            ->latest();

        return TicketDto::collect(
            $query->paginate(
                perPage: $searchDto->perPage,
                page: $searchDto->page,
            ),
        );
    }

    public function createTicket(TicketCreateDto $createDto): void
    {
        Ticket::create([
            ...$createDto->toArray(),
            'status' => TicketStatus::New,
        ]);
    }

    public function updateTicket(TicketUpdateDto $updateDto): void
    {
        /** @var User $user */
        $user = User::findOrFail($updateDto->userId);
        /** @var Ticket $ticket */
        $ticket = Ticket::findOrFail($updateDto->id);

        if ($updateDto->assigned_to !== null) {
            if (! $user->hasRole(UserRole::Dispatcher->value)) {
                throw new \Exception('Only dispatcher can assign technician');
            }

            $ticket->assigned_to = $updateDto->assigned_to;
            $this->setStatus($ticket, $user, TicketStatus::Assigned);
        }

        if ($updateDto->status !== null && $updateDto->status !== $ticket->status) {
            $this->setStatus($ticket, $user, $updateDto->status);
        }

        $ticket->save();
    }

    private function setStatus(Ticket $ticket, User $user, TicketStatus $to): void
    {
        $from = $ticket->status;
        $allowedTargets = [];

        foreach (self::STATUS_ROLES as $role => $targets) {
            if ($user->hasRole($role)) {
                $allowedTargets = array_merge($allowedTargets, $targets);
            }
        }

        if (! in_array($to, $allowedTargets, true)) {
            throw new \Exception('Role cannot set this status');
        }

        if ($user->hasRole(UserRole::Technician->value) && $ticket->assigned_to !== $user->id) {
            throw new \Exception('Not your ticket');
        }

        $allowedTransitions = self::TRANSITIONS[$from->value] ?? [];
        if (! in_array($to, $allowedTransitions, true)) {
            throw new \Exception("Invalid status transition: {$from} -> {$to}");
        }

        $ticket->status = $to;
    }
}
