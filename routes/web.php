<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PokemonController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/index/{offset?}',[PokemonController::class, 'getAllPokemons'])->name('index');