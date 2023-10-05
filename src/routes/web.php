<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('menu');
})->name('menu');

Route::get('/getCargosApi', function () {
    return view('getCargosApi');
})->name('getCargosApi');

Route::get('/cargosUpdater', function () {
    return view('cargosUpdater');
})->name('cargosUpdater');
