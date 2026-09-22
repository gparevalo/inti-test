<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

/*
20 solicitudes por 1 minuto
*/
Route::prefix('v1')->group(function () {
    Route::post('/chat', ChatController::class)
        ->middleware('throttle:20,1');
});