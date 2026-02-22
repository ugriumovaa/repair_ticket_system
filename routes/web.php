<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('tickets.index'));

Route::resource('tickets', TicketController::class)
    ->only(['index', 'create', 'store']);

Route::middleware('auth')->group(function () {
    Route::patch('/tickets/{ticket}', [TicketController::class, 'update'])
        ->name('tickets.update');
});

require __DIR__.'/auth.php';
