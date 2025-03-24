<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\YouTubeController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\StreamingController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// YouTube routes
Route::prefix('youtube')->group(function () {
    Route::get('/connect', [YouTubeController::class, 'redirect'])->name('youtube.connect');
    Route::get('/callback', [YouTubeController::class, 'callback'])->name('youtube.callback');
    Route::get('/channels', [YouTubeController::class, 'channels'])->name('youtube.channels')->middleware('auth');
    Route::get('/stream/{channelId}', [StreamingController::class, 'generateYouTubeStream'])->name('youtube.stream')->middleware('auth');
});

// Facebook routes
Route::prefix('facebook')->group(function () {
    Route::get('/connect', [FacebookController::class, 'redirect'])->name('facebook.connect');
    Route::get('/callback', [FacebookController::class, 'callback'])->name('facebook.callback');
    Route::get('/accounts', [FacebookController::class, 'accounts'])->name('facebook.accounts')->middleware('auth');
    Route::get('/stream/{accountId}', [StreamingController::class, 'generateFacebookStream'])->name('facebook.stream')->middleware('auth');
});

Auth::routes();