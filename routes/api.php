<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;

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


    route::get('category',[CategoryController::class, 'index']);
});
// route::get('category',[CategoryController::class, 'index']);
