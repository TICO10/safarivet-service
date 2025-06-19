<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaCitaController;

Route::get('/test-api', function () {
    return response()->json(['ok' => true]);
});


Route::post('/citas', [AgendaCitaController::class, 'store']);