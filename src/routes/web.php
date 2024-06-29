<?php

use App\Application\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('api/projects', [ProjectController::class, 'getFormattedData']);
