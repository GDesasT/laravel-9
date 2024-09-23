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

Route::get('ahorcado/start/{word}', [AhorcadoController::class, 'start'])
    ->where('word', '[A-Za-z]+');

Route::post('ahorcado/guess', [AhorcadoController::class, 'guess']);

Route::get('ahorcado/status', [AhorcadoController::class, 'status']);
