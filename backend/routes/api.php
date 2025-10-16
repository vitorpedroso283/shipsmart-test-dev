<?php

use App\Http\Controllers\Api\ContactController;
use Illuminate\Support\Facades\Route;

// API de contatos
Route::middleware(['throttle:100,1'])->group(function () {
    Route::get('/contacts/export', [ContactController::class, 'export'])->name('contacts.export');
    Route::get('/contacts', [ContactController::class, 'index'])->name('contact.index');
    Route::get('/contacts/{id}', [ContactController::class, 'show'])->name('contact.show');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contact.store');
    Route::delete('/contacts/{id}', [ContactController::class, 'destroy'])->name('contact.destroy');
    Route::put('/contacts/{id}', [ContactController::class, 'update'])->name('contacts.update');
});
