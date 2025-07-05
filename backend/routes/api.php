<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/login', [AuthController::class, "login"]);
Route::post('/forgot-password', [AuthController::class, "forgot_password"]);
Route::post('/reset-password', [AuthController::class, "reset_password"]);

Route::middleware('auth:api')->prefix('event')->group(function () {
    Route::get('', [EventController::class, "getUserEvents"]);

    Route::post('/create', [EventController::class, "create"]);
});
