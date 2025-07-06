<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Broadcast;
use Laravel\Passport\Http\Middleware\CheckToken;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/login', [AuthController::class, "login"]);
Route::post('/forgot-password', [AuthController::class, "forgot_password"]);
Route::post('/reset-password', [AuthController::class, "reset_password"]);

Route::middleware('auth:api')->group(function () {
    Route::prefix('event')->group(function () {
        Route::get('', [EventController::class, "getUserEvents"]);

        Route::post('/create', [EventController::class, "create"]);
        Route::patch('/{event}/edit', [EventController::class, "edit"]);
        Route::delete('/{event}/delete', [EventController::class, "delete"]);
    });

    Route::prefix('chat')->group(function () {
        Route::get('', [ChatController::class, "getOpenChat"]);
        Route::post('message', [ChatController::class, "message"]);
    });
});

Route::middleware(['auth:api', CheckToken::using('helpdeskAgent')])->group(function () {
    Route::prefix('helpdesk')->group(function () {
        Route::post('message', [ChatController::class, "helpdeskMessage"]);
    });
});
