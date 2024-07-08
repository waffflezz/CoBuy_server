<?php

use App\Http\Controllers\API\Auth\OAuthController;
use App\Http\Controllers\API\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(RegisterController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout');
});

Route::controller(OAuthController::class)->group(function () {
    Route::get('/yandex', 'yandex');
    Route::get('/yandex/redirect', 'yandexRedirect');
});
