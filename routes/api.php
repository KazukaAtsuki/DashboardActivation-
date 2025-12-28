<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoggerAuthController;

    Route::get('/user', function (Request $request) {
    return $request->user();
    })->middleware('auth:sanctum');

    Route::post('/logger/activate', [LoggerAuthController::class, 'activate']);
