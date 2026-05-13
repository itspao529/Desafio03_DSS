<?php

use App\Http\Controllers\MastermindController;
use Illuminate\Support\Facades\Route;

// Rutas obligatorias del desafío [cite: 63, 65, 66, 67]
Route::get('/', [MastermindController::class, 'index']); 
Route::post('/guess', [MastermindController::class, 'guess']); 
Route::post('/restart', [MastermindController::class, 'restart']); 
Route::get('/leaderboard', [MastermindController::class, 'leaderboard']);