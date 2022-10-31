<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\FileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::controller(CustomerController::class)->group(function () {
        Route::get('/customers', 'index');
        Route::post('/customers', 'store');
        Route::get('/customers/{id}', 'show');
    });
    Route::controller(FileController::class)->group(function () {
        Route::post('/files', 'store');
        Route::get('/files/{file}', 'show');
    });
});
