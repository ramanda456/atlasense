<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NegaraApiController;
use App\Http\Controllers\Api\RisikoApiController;
use App\Http\Controllers\Api\PelabuhanApiController;
use App\Http\Controllers\Api\BeritaApiController;
use App\Http\Controllers\Api\KursApiController;

/**
 * =====================================================================
 * API Routes — AtlaSense
 * =====================================================================
 */

Route::get('/countries', [NegaraApiController::class, 'index']);
Route::get('/risk', [RisikoApiController::class, 'index']);
Route::get('/ports', [PelabuhanApiController::class, 'index']);
Route::get('/news', [BeritaApiController::class, 'index']);
Route::get('/currency', [KursApiController::class, 'index']);
