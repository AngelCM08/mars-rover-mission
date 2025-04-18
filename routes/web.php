<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoverPositionController;

Route::get('/', function () {
    return view('index');
});

// Rutas para la API de posición del rover
Route::post('/api/rover/save-position', [RoverPositionController::class, 'savePosition']);
Route::get('/api/rover/get-position', [RoverPositionController::class, 'getPosition']);
