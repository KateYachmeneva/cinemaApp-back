<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//получение токена

Route::post('/token/create', [\App\Http\Controllers\ApiTokenController::class, 'createToken']);

//API администрирования
Route::middleware('auth:sanctum')->get('/user', function () {
    Route::apiResource('/session', \App\Http\Controllers\SessionController::class);
});
