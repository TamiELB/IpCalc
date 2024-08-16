<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


use App\Http\Controllers\IpCalculationController;

// Define the POST route for your API
Route::get('/api/IpCalculation', [IpCalculationController::class, 'index']);