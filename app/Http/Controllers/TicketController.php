<?php

namespace App\Http\Controllers;

use App\Dto\TicketCreateDto;
use App\Dto\UpdateTicketDto;
use App\Models\Ticket;
use App\Models\User;
use App\Enums\TicketStatus;
use App\Services\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\Ticket\StoreRequest;

class TicketController extends Controller
{
    public function __construct(private readonly TicketService $service) {}

    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return Inertia::render('Tickets/Index', [
                'view' => 'guest',
                'tickets' => null,
            ]);
        }

        if ($user->hasRole('dispatcher')) {

            $status = $request->query('status');

            $query = Ticket::query()
                ->with(['assignedTo:id,name'])
                ->latest();

            if ($status && in_array($status, TicketStatus::values(), true)) {
                $query->where('status', $status);
            }

            $technicians = User::role('technician')
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return Inertia::render('Tickets/Index', [
                'view' => 'dispatcher',
                'tickets' => $query->paginate(10)->withQueryString(),
                'filters' => [
                    'status' => $status,
                ],
                'statuses' => TicketStatus::values(),
                'technicians' => $technicians,
            ]);
        }

        if ($user->hasRole('technician')) {
            $tickets = Ticket::query()
                ->where('assigned_to', $user->id)
                ->latest()
                ->paginate(10)
                ->withQueryString();

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
        $this->service->createTask(TicketCreateDto::from($request->validated()));

        return redirect()
            ->route('tickets.create')
            ->with('success', 'Ticket created');
    }

    public function update(UpdateTicketDto $data, Ticket $ticket, TicketService $service)
    {
        $service->update(
            $ticket,
            request()->user(),
            $data->status,
            $data->assigned_to,
        );

        return back()->with('success', 'Updated');
    }

}
