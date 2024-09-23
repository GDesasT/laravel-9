<?php

use App\Http\Controllers\AhorcadoController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/juego', [AhorcadoController::class, 'iniciarJuego']);
Route::post('/juego/{id}/intento', [AhorcadoController::class, 'hacerIntento']);
Route::get('/juego/{id}', [AhorcadoController::class, 'obtenerEstado']);