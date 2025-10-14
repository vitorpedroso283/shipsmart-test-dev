<?php

use App\Http\Controllers\Api\ContactController;
use Illuminate\Support\Facades\Route;

// API de contatos
Route::get('/contacts', [ContactController::class, 'index'])->name('contact.index');
