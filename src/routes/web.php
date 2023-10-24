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

Route::get('/getCargosApi/submit',
    'App\Models\Modules\Api\Models\ApiAsker@makeRequest')->name('initialDataShower');

Route::post('/chooseRequestType/submit',
    'App\Models\Modules\Api\Models\ApiAsker@chooseRequestType')->name('chooseRequestType');

Route::get('/get_page_count', 'App\Models\Modules\Api\Models\ApiAsker@getPageCount')->name('getPageCount');

Route::get('/getLoadData', 'App\Models\Modules\Api\Models\ApiGetter@getLoadData');
