<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaCitaController;

Route::get('/', function () {
    return view('welcome');
});

