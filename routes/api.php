<?php

use App\Http\Controllers\Api\TicketController;
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

// Ruta para obtener los tickets (órdenes) del usuario autenticado
Route::middleware('auth:sanctum')->get('/my-tickets', [TicketController::class, 'myTickets']); // Cambiado de my-orders a my-tickets
// Ruta para obtener los detalles de un ticket (orden) específico
Route::middleware('auth:sanctum')->get('/tickets/{id}', [TicketController::class, 'ticketDetails']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::middleware('auth:sanctum')->get('/my-orders', [MyOrdersController::class, 'index']);
