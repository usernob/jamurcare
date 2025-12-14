<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get("/monitoring/{ulid}", [DashboardController::class, "getMonitoringData"]);
Route::get("/ping/{ulid}", [DashboardController::class, "pingDevice"]);
Route::post("/control/{ulid}", [DashboardController::class, "controlDevice"]);
