<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CashierHallWiseController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerAdvancedtController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DaySummaryReport;
use App\Http\Controllers\Api\FloorController;
use App\Http\Controllers\Api\FloorSectionController;
use App\Http\Controllers\Api\GraphController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\ItemSaleReportController;
use App\Http\Controllers\Api\ModifierController;
use App\Http\Controllers\Api\ModifierGroupController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\TablesController;
use App\Http\Controllers\Api\TaxController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\DashboardController;
use App\Models\Role;
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
Route::post('userregister', [AuthController::class, 'register']);
Route::post('userlogin', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::apiResource('category', CategoryController::class);

    Route::get('getItems/{category_id}', [CategoryController::class, 'getItems']);

    Route::apiResource('table', TablesController::class);

    Route::get('getSections', [TablesController::class, 'getSections']);

    Route::post('setSection', [TablesController::class, 'setSection']);

    Route::post('setTables', [TablesController::class, 'setTables']);

    Route::post('updateSection', [TablesController::class, 'updateSection']);

    Route::post('deleteSection', [TablesController::class, 'deleteSection']);

    Route::apiResource('items', ItemController::class);

    Route::get('showModifierGroups/{item_id}', [ItemController::class, 'showModifierGroups']);

    Route::get('selectModifierGroups/{item_id}', [ItemController::class, 'selectModifierGroups']);

    Route::post('saveModifierGroups/{item_id}', [ItemController::class, 'saveModifierGroups']);

    Route::get('getSectionPrice', [ItemController::class, 'getSectionPrice']);

    Route::post('setSectionPrice',[ItemController::class, 'setSectionPrice']);

    Route::post('updateSectionPrice', [ItemController::class, 'updateSectionPrice']);

    Route::post('deleteSectionPrice', [ItemController::class, 'deleteSectionPrice']);

    Route::apiResource('floor', FloorController::class);

    Route::apiResource('order', OrderController::class);

    Route::apiResource('customer', CustomerController::class);

    Route::apiResource('cart', CartController::class);

    Route::apiResource('graph', GraphController::class);
    
    Route::apiResource('itemsale', ItemSaleReportController::class);

    route::apiResource('tax', TaxController::class);

    Route::get('/getFloorsAndTables', [TablesController::class, 'getFloorsAndTables']);

    Route::post('setSectionAndTables',[FloorController::class, 'setSectionAndTables']);

    Route::get('getOrdersBill', [OrderController::class, 'getOrdersBill']);

    Route::get('/getTableId/{section}', [OrderController::class, 'getTableId']);

    Route::post('order-confirm', [OrderController::class, 'confirmOrder']);

    Route::post('update-item', [OrderController::class, 'updateItem']);

    Route::post('add-item', [OrderController::class, 'addItem']);

    Route::post('cancel-item', [OrderController::class, 'cancelItem']);

    Route::post('cancel-order', [OrderController::class, 'cancelOrder']);

    Route::post('complete-order', [OrderController::class, 'completeOrder']);

    Route::post('order-status', [OrderController::class, 'orderStatus']);

    Route::apiResource('staff', StaffController::class);

    Route::post('top-selling-items', [DashboardController::class, 'topSellingItems']);

    Route::post('dashboard-cards', [DashboardController::class, 'dashboardCards']);

    Route::get('cash-payment', [DashboardController::class, 'cashPaymentAmount']);

    Route::get('online-payment', [DashboardController::class, 'onlinePaymentAmount']);
    // Route::post("online-payment",[DashboardController::class, 'onlinePaymentAmount']);
    Route::get('day-summary-report', [DaySummaryReport::class, 'index']);

    route::apiResource('section', SectionController::class);

    route::apiResource('floorsection', FloorSectionController::class);

    Route::get('cancel-items', [DaySummaryReport::class, 'cancelItemsReport']);

    route::apiResource('cashier-hallwise', CashierHallWiseController::class);

    Route::get('cashier-report', [DaySummaryReport::class, 'cashierReport']);

    Route::get('cancel-order-report', [DaySummaryReport::class, 'cancelOrderReport']);

    Route::get('itemtotalreport',[ItemSaleReportController::class,'itemtotalreport']);

    Route::get('getActiveTables', [OrderController::class, 'getActiveTables']);

    Route::apiResource('customerAddress',CustomerAdvancedtController::class);

    // Modifiers Groups Apis - Modifiers & Items
    Route::apiResource('modifierGroups', ModifierGroupController::class);

    Route::get('showModifiers/{mod_grp_id}', [ModifierGroupController::class, 'showModifiers']);

    Route::get('selectModifiers/{mod_grp_id}', [ModifierGroupController::class, 'selectModifiers']);

    Route::post('saveModifiers/{mod_grp_id}', [ModifierGroupController::class, 'saveModifiers']);

    Route::get('showItems/{mod_grp_id}', [ModifierGroupController::class, 'showItems']);

    Route::get('selectItems/{mod_grp_id}', [ModifierGroupController::class, 'selectItems']);

    Route::post('saveItems/{mod_grp_id}', [ModifierGroupController::class, 'saveItems']);

    // Modifier Apis - Modifiers Groups
    Route::apiResource('modifiers', ModifierController::class);

    Route::get('showModifierGroups/{modifier_id}', [ModifierController::class, 'showModifierGroups']);

    Route::get('selectModifierGroups/{modifier_id}', [ModifierController::class, 'selectModifierGroups']);

    Route::post('saveModifierGroups/{modifier_id}', [ModifierGroupController::class, 'saveModifierGroups']);
});
