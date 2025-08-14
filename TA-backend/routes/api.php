<?php

use App\Http\Controllers\AspirationController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {   
    return $request->user();
});

Route::post("/login", [AuthController::class, "login"]);

Route::middleware("auth:sanctum","admin" )->group(function(){
    Route::get("/aspirations", [AspirationController::class, "index"]);
    Route::get("/aspirations/{aspiration}", [AspirationController::class, "show"]);
    Route::post("/logout", [AuthController::class, "logout"]);
});

Route::post("/aspirations", [AspirationController::class, "store"]);
