<?php

use App\Http\Controllers\Api\ContactController;
use Illuminate\Support\Facades\Route;

// API de contatos
Route::middleware(['throttle:100,1'])->group(function () {
    // Rota específica para exportação
    Route::get('/contacts/export', [ContactController::class, 'export'])->name('contacts.export');

    // Rotas agrupadas usando apiResource para CRUD
    Route::apiResource('contacts', ContactController::class);
});
