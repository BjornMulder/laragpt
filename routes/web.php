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
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {

    Route::get('/chat', function () {
        return view('chat');
    })->name('chat');

    Route::get('/compare', function () {
        return view('compare');
    })->name('compare');

    Route::get('/image', function () {
        return view('image');
    })->name('image');

    Route::post('/chat-gpt', 'App\Http\Controllers\ChatGPTController@generateResponse')->name('chat-gpt.generate');
});

