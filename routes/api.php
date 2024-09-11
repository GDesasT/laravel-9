<?php

use App\Http\Controllers\TableController;
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

Route::get('tablitas/{value1}/{value2?}/{fibonacci?}', [TableController::class, 'index'])
    ->where('value1', '[0-9]+')
    ->where('value2', '[0-9]+')
    ->where('fibonacci', 'fibonacci');
