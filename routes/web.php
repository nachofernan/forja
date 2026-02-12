<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Game;

// Main game route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Livewire component route
Route::get('/game', Game::class)->name('game');
