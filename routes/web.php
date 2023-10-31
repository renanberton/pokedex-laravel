<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PokemonController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/',[PokemonController::class, 'getAllPokemons'])->name('index');
Route::get('/index/{offset?}',[PokemonController::class, 'getAllPokemons'])->name('index');
Route::get('/search-pokemon',[PokemonController::class, 'searchPokemon'])->name('searchPokemon');
Route::get('/pokemon-not-found',[PokemonController::class, 'pokemon-not-found'])->name('pokemon-not-found');
