<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\Social\DiscordController;
use App\Http\Controllers\Auth\Social\FacebookController;
use App\Http\Controllers\Auth\Social\GoogleController;
use App\Http\Controllers\Auth\Social\SteamController;
use App\Http\Controllers\Auth\Social\VkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->post('/update', [LoginController::class, 'update']);
Route::post('resetPassword', [LoginController::class, 'resetPassword']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/demoLogin', [LoginController::class, 'demologin']);
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/vk', [VkController::class, 'vk']);
Route::get('/discord', [DiscordController::class, 'discord']);
Route::get('/steam', [SteamController::class, 'steam']);
Route::get('/fb', [FacebookController::class, 'facebook']);
Route::get('/google', [GoogleController::class, 'google']);

Route::get('/logout', [LogoutController::class, 'logout']);
