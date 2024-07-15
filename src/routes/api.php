<?php

use App\Http\Controllers\API\Auth\OAuthController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\GroupController;
use App\Http\Controllers\API\GroupInviteController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ShoppingListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(RegisterController::class)->group(function () {
    Route::post('/register', 'register');

    Route::get('/login/check', 'loginCheck')->middleware('auth:sanctum');
    Route::post('/login', 'login');

    Route::post('/logout', 'logout');
});

Route::controller(OAuthController::class)->group(function () {
    Route::get('/yandex', 'yandex');
    Route::get('/yandex/redirect', 'yandexRedirect');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('group', GroupController::class);
    Route::post('/group/{group}/leave', [GroupController::class, 'leave']);
    Route::post('/group/kick', [GroupController::class, 'kick']);

    Route::apiResource('list', ShoppingListController::class);
    Route::apiResource('list.product', ProductController::class);

    Route::get('/invite/{group}', [GroupInviteController::class, 'getInviteLink']);
    Route::get('/invite', [GroupInviteController::class, 'invite']);
});
