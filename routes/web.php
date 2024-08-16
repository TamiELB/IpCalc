<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IpCalculationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/IpCalculation', [IpCalculationController::class, 'index']);