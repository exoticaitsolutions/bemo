<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardController;
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

Route::get('card-list', [CardController::class, 'cardList']);
Route::post('add-card', [CardController::class, 'addNewCard']);

Route::post('add-item', [CardController::class, 'addNewItem']);

Route::delete('delete-item/{id}', [CardController::class, 'deleteItem']);
Route::delete('delete-card/{id}', [CardController::class, 'deleteCard']);


