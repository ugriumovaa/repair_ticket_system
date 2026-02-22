<?php

namespace App\Http\Controllers;

use App\Dto\Ticket\TicketCreateDto;
use App\Dto\Ticket\TicketSearchDto;
use App\Dto\Ticket\TicketUpdateDto;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Requests\Ticket\IndexRequest;
use App\Http\Requests\Ticket\StoreRequest;
use App\Models\Ticket;
use App\Services\TechnicianService;
use App\Services\TicketService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService,
        private readonly TechnicianService $technicianService,
    ) {}

    public function index(IndexRequest $request)
    {
        $user = $request->user();

        if (!$user) {
            return Inertia::render('Tickets/Index', [
                'view' => 'guest',
                'tickets' => null,
            ]);
        }

        if ($user->hasRole(UserRole::Dispatcher->value)) {
            $status = $request->validated('status');
            $searchDto = new TicketSearchDto(
                status: $status ? TicketStatus::from($status) : null,
                page: $request->validated('page') ?? 1,
                perPage: 10,
            );

           $tickets = $this->ticketService->getTickets($searchDto);
           $technicians = $this->technicianService->getTechnicians();

            return Inertia::render('Tickets/Index', [
                'view' => 'dispatcher',
                'tickets' => $tickets,
                'filters' => [
                    'status' => $status,
                ],
                'statuses' => TicketStatus::values(),
                'technicians' => $technicians,
            ]);
        }

        if ($user->hasRole(UserRole::Technician->value)) {

            $searchDto = new TicketSearchDto(
                assigned_to: $user->id,
                page: $request->validated('page') ?? 1,
                perPage: 10,
            );

            $tickets = $this->ticketService->getTickets($searchDto);

            return Inertia::render('Tickets/Index', [
                'view' => 'technician',
                'tickets' => $tickets,
            ]);
        }
    }

    public function create(): Response
    {
        return Inertia::render('Tickets/Create');
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $this->ticketService->createTicket(TicketCreateDto::from($request->validated()));

        return redirect()
            ->route('tickets.create')
            ->with('success', 'Ticket created');
    }

    public function update(TicketUpdateDto $data, Ticket $ticket, TicketService $service)
    {
        $service->updateTicket(
            $ticket,
            request()->user(),
            $data->status,
            $data->assigned_to,
        );

        return back()->with('success', 'Updated');
    }

}
