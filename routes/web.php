<?php

use App\Http\Controllers\FormQuestionController;
use App\Http\Controllers\AspirationController;
use App\Http\Controllers\AspirationEventController;
use App\Http\Controllers\AspirationKeluhKesahController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\TargetEmailController;
use Illuminate\Support\Facades\Route;

Route::get('/detail', function () {
    return view('details.detail');
});

Route::get('/fallback', function () {
    return view('fallback');
})->name('fallback');

//voxes form
Route::get('/', [AspirationController::class, "aspirationForm"])->name('aspirations.create');
//voxes store logic — ponytail: throttle cegah spam flood
Route::post('/aspirations', [AspirationController::class, 'store'])->middleware('throttle:20,1')->name('aspirations.store');

//event form
Route::get('/event', [EventController::class, 'getEvent'])->name('event');
//event store logic
Route::post('/aspiration-events', [AspirationEventController::class, 'store'])->middleware('throttle:20,1')->name('aspiration_events.store');

//keluh kesah form
Route::get('/keluh-kesah', [AspirationKeluhKesahController::class, "aspirationForm"]);
//keluh kesah store logic
Route::post('/aspiration-keluhkesah', [AspirationKeluhKesahController::class, 'store'])->middleware('throttle:20,1')->name('aspiration_keluhkesah.store');

//authentication
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//dashboard utama — hanya admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [EventController::class, 'index'])->name('dashboard');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::patch('/events/{event}/visibility', [EventController::class, 'toggleVisibility'])->name('events.toggle-visibility');
});

//page aspirasi gelar wicara — hanya admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/aspirations', [AspirationController::class, 'index'])->name('aspirations.index');
    Route::put('/aspirations/{id}', [AspirationController::class, 'update'])->name('aspirations.update');
    Route::delete('/aspirations/{id}', [AspirationController::class, 'destroy'])->name('aspirations.destroy');

    // Bulk Delete
    Route::post('/aspirations/bulk-delete', [AspirationController::class, 'bulkDestroy'])->name('aspirations.bulk-destroy');

    // Export CSV
    Route::get('/aspirations/export/csv', [AspirationController::class, 'exportCsv'])->name('aspirations.export');
});

Route::middleware('auth')->group(function () {
    Route::get('/aspiration-keluh-kesah', [AspirationKeluhKesahController::class, 'index'])->name('aspiration_keluhkesah.index');
    Route::get('/aspiration-keluh-kesah/{id}', [AspirationKeluhKesahController::class, 'show'])->name('aspiration_keluhkesah.show');
    Route::delete('/aspiration-keluh-kesah/{id}', [AspirationKeluhKesahController::class, 'destroy'])->name('aspiration_keluhkesah.destroy');
    Route::post('/aspiration-keluh-kesah/bulk-delete', [AspirationKeluhKesahController::class, 'bulkDestroy'])->name('aspiration_keluhkesah.bulk-destroy');

    // Export CSV for keluh-kesah (respecting filters passed as query string)
    Route::get('/aspiration-keluh-kesah/export/csv', [AspirationKeluhKesahController::class, 'exportCsv'])->name('aspiration_keluhkesah.export');
});

// API routes for paginated data — hanya admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/api/aspirations', [AspirationController::class, 'fetchPaginated'])->name('api.aspirations');
    Route::get('/api/aspiration-events/{eventId}', [AspirationEventController::class, 'fetchPaginatedByEvent'])->name('api.aspiration_events.by_event');
    Route::get('/api/aspiration-keluh-kesah', [AspirationKeluhKesahController::class, 'fetchPaginated'])->name('api.aspiration_keluhkesah');
    Route::get('/api/statistics', [EventController::class, 'statistics'])->name('api.statistics');
});

//page aspirasi tiap event — hanya admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::put('/aspiration-events/{id}', [AspirationEventController::class, 'update'])->name('aspiration_events.update');
    Route::delete('/aspiration-events/{id}', [AspirationEventController::class, 'destroy'])->name('aspiration_events.destroy');
    Route::post('/aspiration-events-bulk-delete', [AspirationEventController::class, 'bulkDestroy'])->name('aspiration_events.bulk-destroy');

    // Menampilkan aspirasi berdasarkan event
    Route::get('/aspiration-events/event/{eventId}', [AspirationEventController::class, 'showAspirationByEvent'])
        ->name('aspiration_events.by_event');

    // Export CSV
    Route::get('/aspiration-events/export/{eventId}', [AspirationEventController::class, 'exportCsv'])
        ->name('aspiration_events.export');
});

// Share routes — hanya admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/api/share', [ShareController::class, 'store'])->name('api.share');
});

Route::get('/share/{token}', [ShareController::class, 'show'])->name('share.show');
Route::get('/api/share/{token}/data', [ShareController::class, 'fetchShared'])->name('api.share.data');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::put('/form-questions/{formType}', [FormQuestionController::class, 'update'])->name('form_questions.update');
    Route::put('/form-questions/{formType}/{entityId}', [FormQuestionController::class, 'update'])->name('form_questions.update_entity');
    Route::post('/form-questions/{formType}/reset', [FormQuestionController::class, 'reset'])->name('form_questions.reset');
    Route::post('/form-questions/{formType}/{entityId}/reset', [FormQuestionController::class, 'reset'])->name('form_questions.reset_entity');
});

// Target email routes — hanya admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/api/target-emails', [TargetEmailController::class, 'index'])->name('target_emails.index');
    Route::post('/api/target-emails', [TargetEmailController::class, 'store'])->name('target_emails.store');
    Route::delete('/api/target-emails/{id}', [TargetEmailController::class, 'destroy'])->name('target_emails.destroy');
    Route::post('/api/target-emails/{id}/toggle', [TargetEmailController::class, 'toggle'])->name('target_emails.toggle');
});

require __DIR__ . '/auth.php';
