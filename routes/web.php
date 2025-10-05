<?php

use App\Http\Controllers\AspirationController;
use App\Http\Controllers\AspirationEventController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('aspiration_forms.voxes-form');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::post('/aspirations', [AspirationController::class, 'store'])->name('aspirations.store');
Route::post('/aspiration-events', [AspirationEventController::class, 'store'])->name('aspiration_events.store');

//dashboard utama
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [EventController::class, 'index'])->name('dashboard');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
});

//page aspirasi gelar wicara
Route::middleware('auth')->group(function () {
    Route::get('/aspirations', [AspirationController::class, 'index'])->name('aspirations.index');
    Route::get('/aspirations/create', [AspirationController::class, 'create'])->name('aspirations.create');
    Route::get('/aspirations/{id}', [AspirationController::class, 'show'])->name('aspirations.show');
    Route::get('/aspirations/{id}/edit', [AspirationController::class, 'edit'])->name('aspirations.edit');
    Route::put('/aspirations/{id}', [AspirationController::class, 'update'])->name('aspirations.update');
    Route::delete('/aspirations/{id}', [AspirationController::class, 'destroy'])->name('aspirations.destroy');

    // Export CSV
    Route::get('/aspirations/export/csv', [AspirationController::class, 'exportCsv'])->name('aspirations.export');
});

//page aspirasi tiap event
Route::middleware('auth')->group(function () {
    Route::get('/aspiration-events', [AspirationEventController::class, 'index'])->name('aspiration_events.index');
    Route::get('/aspiration-events/create', [AspirationEventController::class, 'create'])->name('aspiration_events.create');
    Route::get('/aspiration-events/{id}', [AspirationEventController::class, 'show'])->name('aspiration_events.show');
    Route::get('/aspiration-events/{id}/edit', [AspirationEventController::class, 'edit'])->name('aspiration_events.edit');
    Route::put('/aspiration-events/{id}', [AspirationEventController::class, 'update'])->name('aspiration_events.update');
    Route::delete('/aspiration-events/{id}', [AspirationEventController::class, 'destroy'])->name('aspiration_events.destroy');

    // Menampilkan aspirasi berdasarkan event
    Route::get('/aspiration-events/event/{eventId}', [AspirationEventController::class, 'showAspirationByEvent'])
        ->name('aspiration_events.by_event');

    // Export CSV
    Route::get('/aspiration-events/export/{eventId}', [AspirationEventController::class, 'exportCsv'])
        ->name('aspiration_events.export');
});

require __DIR__ . '/auth.php';
