<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CashierHallWiseController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CateringConfirmController;
use App\Http\Controllers\Api\CustomerAddressController;
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
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\TablesController;
use App\Http\Controllers\Api\TaxController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventorySupplierController;
use App\Models\ModifierGroup;
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

    Route::get('category2', [CategoryController::class, 'index2']);

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

    Route::post('update-quantity',[OrderController::class, 'updateItemQuantity']);

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
    
    Route::get('day-summary-report', [DaySummaryReport::class, 'index']);

    route::apiResource('section', SectionController::class);

    route::apiResource('floorsection', FloorSectionController::class);

    Route::get('cancel-items', [DaySummaryReport::class, 'cancelItemsReport']);

    route::apiResource('cashier-hallwise', CashierHallWiseController::class);

    Route::get('cashier-report', [DaySummaryReport::class, 'cashierReport']);

    Route::get('cancel-order-report', [DaySummaryReport::class, 'cancelOrderReport']);

    Route::get('itemtotalreport',[ItemSaleReportController::class,'itemtotalreport']);

    Route::get('getActiveTables', [OrderController::class, 'getActiveTables']);

    Route::apiResource('customerAddress',CustomerAddressController::class);

    Route::get('getCustomerAddresses/{customerId}', [CustomerAddressController::class, 'getCustomerAddresses']);

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

    //inventory

      //inventory supplier     
      Route::get('supplier-list',[InventorySupplierController::class, 'inventoryList']);
      Route::post('create-supplier',[InventorySupplierController::class, 'createSupplier']);
      Route::put('/suppliers/{id}', [InventorySupplierController::class, 'updateSupplier']);
      Route::delete('/suppliers/{id}', [InventorySupplierController::class, 'deleteSupplier']);      
      Route::get('suppliers/search', [InventorySupplierController::class, 'searchSupplier']);



  
     
  

    //bulk upload
    Route::post('bulk-item', [ItemController::class, 'bulkUploadItems']);
    
    Route::post('bulk-category', [CategoryController::class, 'bulkUploadCategories']);

    Route::get('getTotalOrders/{tab}',[OrderController::class, 'getTotalOrders']);

    Route::get('/export-categories',[ItemController::class, 'exportCategories']);

    Route::post('update-status-delivery', [OrderController::class, 'delivery_status_kot']);

    Route::get('get-ongoing-orders', [OrderController::class, 'getOngoingOrders']);

    Route::get('getDeliveryPendingOrders', [OrderController::class, 'getDeliveryPendingOrders']);
    
    Route::get('getDeliveryCompletedOrders', [OrderController::class, 'getDeliveryCompletedOrders']);

    Route::post('importModifierGroups', [ModifierGroupController::class, 'importModifierGroups']);
    
    Route::post('importModifiers', [ModifierController ::class, 'importModifiers']);

    Route::get('exportModifierGroups', [ModifierGroupController::class, 'exportModifierGroups']);

    Route::get('exportModifiers', [ModifierController::class, 'exportModifiers']);

    Route::put("updateProfile/{id}", [AuthController::class, 'updateProfile']);

    Route::put("updateRestaurant/{rest_id}", [AuthController::class, 'updateRestaurant']);

    //catering conform /

    Route::post('catering-order-confirm', [CateringConfirmController::class, 'cateringConfirmOrder']);

    Route::get('catering-order-bill', [CateringConfirmController::class, 'cateringOrderBill']);

    Route::post('catering-item-update',[CateringConfirmController::class, 'updateItemCat']);

    Route::post('catering-add-item',[CateringConfirmController::class,'additemCat']);

    Route::post('catering-cancel-item',[CateringConfirmController::class,'cancelItemCatering']);

    Route::post('complete-order-catering',[CateringConfirmController::class,'completeOrderCatering']);

    Route::post('cancel-order-catering', [CateringConfirmController::class, 'cancelOrderCatering']);

    Route::get('pending-order-catering', [CateringConfirmController::class, 'cateringPendingOrders']);

    Route::post('partial-catering-payment',[CateringConfirmController::class,'partialOrderPayment']);

    Route::post('setSectionWisePrice',[ItemController::class, 'setSectionWisePrice']);
    
    Route::get('getItemsBySectionId/{section_id}',[ItemController::class, 'getItemsBySectionId']);
    
    Route::get('getCategoryItemsBySectionId/{category_id}/{section_id}',[ItemController::class, 'getCategoryItemsBySectionId']);

    Route::post('tax-setting',[OrderController::class,'tax_setting']);

    Route::get('get-tax',[OrderController::class,'get_tax']);



});
