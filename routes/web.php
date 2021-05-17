<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FileUploadController;
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

// Get the inital page (get)
Route::get('file-upload', [FileUploadController::class, 'fileUpload'])->name('file.upload');  

// Get file list & url list from read dir and parse urls file
// Route::get('file-list', [FileUploadController::class, 'getFileList'])->name('file.list');
Route::get('file-list-v2', [FileUploadController::class, 'getFileListV2'])->name('file.listv2');

// Upload the file (post)
Route::post('file-upload', [FileUploadController::class, 'fileUploadPost'])->name('file.upload.post');

// Delete an url
Route::post('url-delete', [FileUploadController::class, 'urlDelete'])->name('url.delete.post');

// Delete an url
Route::post('file-delete', [FileUploadController::class, 'fileDelete'])->name('file.delete.post');

// Save params in files 
Route::post('save-kiosks', [FileUploadController::class, 'saveKiosks'])->name('savekiosks.post');

/*
Route::get('/help', function () {
    return view('help');
})->name('help');
`*/