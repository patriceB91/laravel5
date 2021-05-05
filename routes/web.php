<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/about', function () {
    return view('about');
})->name('about');


// Syntaxe OK pour PHP 7.4
// Route::get('/help', fn() => view('help') )->name('help')

// Short Way 
Route::view('/help', 'help')->name('help');

/*
Route::get('/help', function () {
    return view('help');
})->name('help');
`*/