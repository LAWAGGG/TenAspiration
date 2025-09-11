<?php

use App\Http\Controllers\AspirationController;
use App\Http\Controllers\AspirationEventController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {   
    return $request->user();
});

Route::post("/login", [AuthController::class, "login"]);

//Admin Only
Route::middleware("auth:sanctum","admin" )->group(function(){
    //Aspiration (daily)
    Route::get("/aspirations", [AspirationController::class, "index"]);
    Route::get("/aspirations/{aspiration}", [AspirationController::class, "show"]);
    Route::delete("/aspirations/{aspiration}", [AspirationController::class, "destroy"]);

    //Aspiration (event)
    Route::get("/aspiration/events", [AspirationEventController::class, "index"]);
    Route::get("/aspiration/events/{aspirationEvent}", [AspirationEventController::class, "show"]);
    Route::get("/aspiration/events/event/{eventId}", [AspirationEventController::class, "showAspirationByEvent"]);
    Route::delete("/aspiration/events/{aspirationEvent}", [AspirationEventController::class, "destroy"]);

    //Event Management
    Route::post("/events", [EventController::class, "store"]);
    Route::get("/events/{event}", [EventController::class, "show"]);
    Route::put("/events/{event}", [EventController::class, "update"]);
    Route::delete("/events/{event}", [EventController::class, "destroy"]);
    
    //logout user
    Route::post("/logout", [AuthController::class, "logout"]);
});
Route::get("/events", [EventController::class, "index"]);

//User (public)
Route::post("/aspirations", [AspirationController::class, "store"]);
Route::post("/aspiration/events", [AspirationEventController::class, "store"]);
