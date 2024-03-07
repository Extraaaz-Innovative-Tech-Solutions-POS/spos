<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FloorController;
use App\Http\Controllers\Api\ItemsController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\TablesController;

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
route::post('userregister', [AuthController::class, 'register']);
route::post('userlogin', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group( function () {
    // return $request->user()


    route::apiResource('category',CategoryController::class);
    route::apiResource('table',TablesController::class);
    route::apiResource('items',ItemsController::class);
    route::apiResource('floor',FloorController::class);
    route::apiResource('order',OrderController::class);
});
// route::get('category',[CategoryController::class, 'index']);
