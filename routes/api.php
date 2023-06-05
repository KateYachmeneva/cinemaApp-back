<?php

use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//получение токена

Route::post('/token/create', [ApiTokenController::class, 'createToken']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// API администрирования

Route::get('hall', [HallController::class, 'index']);
Route::get('film', [FilmController::class, 'index']);
Route::get('seance', [SessionController::class, 'index']);


// ** Public file route **//
Route::get('files/{fileName}', [FileController::class, 'loadFile']);

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::post('/token/clear', [ApiTokenController::class, 'clearToken']);

    Route::post('film', [FilmController::class, 'store']);
    Route::put('film/{id}', [FilmController::class, 'update']);
    Route::delete('film/{id}', [FilmController::class, 'destroy']);
    Route::get('film/{id}', [FilmController::class, 'show']);

    Route::post('hall', [HallController::class, 'store']);
    Route::put('hall/{id}', [HallController::class, 'update']);
    Route::delete('hall/{id}', [HallController::class, 'destroy']);
    Route::get('hall/{id}', [HallController::class, 'show']);


    Route::get('/seats', [SeatController::class,'index']);
    Route::post('/seats', [SeatController::class, 'store']);
    Route::put('/seats/update', [SeatController::class, 'updateMany']);
    Route::get('seats/{id}', [SeatController::class, 'show']);

    Route::post('seance', [SessionController::class, 'store']);
    Route::put('seance/{id}', [SessionController::class, 'update']);
    Route::delete('seance/{id}', [SessionController::class, 'destroy']);
    Route::get('seance/{datetime}', [SessionController::class, 'show']);
});

// API части для Клиента

Route::get('/client/schedule/{date}', [ClientController::class, 'scheduleAvailable']);
Route::get('/client/seats/{session}', [ClientController::class, 'seatsAvailable']);
Route::post('/ticket', [TicketController::class, 'store']);
Route::get('/ticket/{id}', [TicketController::class, 'show']);