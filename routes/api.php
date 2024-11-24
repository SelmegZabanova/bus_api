<?php

use App\Http\Controllers\Api\BusController;
use App\Http\Controllers\Api\RouteDirectionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RouteController;


Route::get('/find-bus', [BusController::class, 'findBus']);

Route::prefix('routes')->group(function () {
    Route::get('/', [RouteController::class, 'index']);

    Route::put('/{route}', [RouteController::class, 'update']);
});

Route::prefix('route-directions')->group(function () {
    Route::put('/{routeDirection}', [RouteDirectionController::class, 'update']);
});
