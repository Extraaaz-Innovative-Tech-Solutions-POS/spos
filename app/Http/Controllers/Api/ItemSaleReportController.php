<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\KotItem;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemSaleReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $user = Auth::user();
        $selectedFromDate = $request->fromdate;
        $selectedToDate = $request->todate;

        $productsCount = KotItem::where('restaurant_id', $user->restaurant_id)
        ->select('name', 'quantity','price','product_total','is_cancelled',)
        ->get();

        // $totalProductsCount = $productsCount->sum('quantity');

        foreach ($productsCount as $product) {
            $orderDiscount = Order::where('restaurant_id', $user->restaurant_id)
                // ->where('product_id', $product->id) // Assuming there is a product_id column in the Order table
                ->value('total_discount');
            $product->discount = $orderDiscount;
        }
        
        $totalSale = Order::where('restaurant_id', $user->restaurant_id)
        ->select('total_discount')
        ->get();

    $restaurantName = Restaurant::where('id', $user->restaurant_id)->value('restaurant_name');
    return response()->json([
    "success" => true,
    "restaurantName" => $restaurantName,
    "productsCount" => $productsCount,
   
    'totalSale' => $totalSale
    
]);

           





    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
