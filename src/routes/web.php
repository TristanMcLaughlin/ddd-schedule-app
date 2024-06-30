<?php

use App\Application\Http\Controllers\CustomDatePeriodController;
use App\Application\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('api/projects', [ProjectController::class, 'getFormattedData']);
Route::post('api/projects/{projectId}/date-periods', [CustomDatePeriodController::class, 'store']);
