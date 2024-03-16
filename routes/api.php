<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CashierHallWiseController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DaySummaryReport;
use App\Http\Controllers\Api\FloorController;
use App\Http\Controllers\Api\FloorSectionController;
use App\Http\Controllers\Api\ItemsController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\TablesController;
use App\Http\Controllers\Api\TaxController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\DashboardController;
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
route::post('userregister', [AuthController::class, 'register']);
route::post('userlogin', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group( function () {
    // return $request->user()

    Route::apiResource('category',CategoryController::class);
    
    Route::get('table',[TablesController::class,'get']);
    
    Route::post('setSection',[TablesController::class,'setSection']);
    
    Route::post('setTables',[TablesController::class,'setTables']);

    Route::post('deleteSection',[TablesController::class,'deleteSection']);
    
    Route::apiResource('items',ItemsController::class);
    Route::apiResource('floor',FloorController::class);
    Route::apiResource('order',OrderController::class);
    Route::apiResource('customer',CustomerController::class);
    Route::apiResource('cart',CartController::class);
    // route::apiResource('tax',TaxController::class);

    Route::get('/getFloorsAndTables',[TablesController::class, 'getFloorsAndTables']);

    Route::get('getOrdersBill',[OrderController::class, 'getOrdersBill']);

    Route::get("/getTableId/{section}", [OrderController::class, 'getTableId']);

    Route::post('order-confirm', [OrderController::class, 'confirmOrder']);

    Route::post('update-item', [OrderController::class, 'updateItem']);

    Route::post('add-item', [OrderController::class, 'addItem']);

    Route::post("cancel-item",[OrderController::class, 'cancelItem']);
    
    Route::post("cancel-order",[OrderController::class, 'cancelOrder']);
    
    Route::post("complete-order",[OrderController::class, 'completeOrder']);
    
    Route::post("order-status",[OrderController::class, 'orderStatus']);

    Route::apiResource('staff', StaffController::class);
    
    Route::post("top-selling-items",[DashboardController::class, 'topSellingItems']);

    Route::post("dashboard-cards",[DashboardController::class, 'dashboardCards']);

    Route::get("cash-payment",[DashboardController::class, 'cashPaymentAmount']);

    Route::get("online-payment",[DashboardController::class, 'onlinePaymentAmount']);
    // Route::post("online-payment",[DashboardController::class, 'onlinePaymentAmount']);
    Route::get("day-summary-report",[DaySummaryReport::class, 'index']);
    route::apiResource('section', SectionController::class);

    route::apiResource('floorsection',FloorSectionController::class);

    Route::get("cancel-items",[DaySummaryReport::class, 'cancelItemsReport']);
    
    route::apiResource("cashier-hallwise",CashierHallWiseController::class);


    Route::get("cashier-report",[DaySummaryReport::class, 'cashierReport']);
    
    Route::get("cancel-order",[DaySummaryReport::class, 'cancelOrderReport']);

    Route::get('getActiveTables',[OrderController::class, 'getActiveTables']);
});
// route::get('category',[CategoryController::class, 'index']);
