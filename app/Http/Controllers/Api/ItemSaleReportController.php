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
        $selectedFromDate = $request->fromDate;
        $selectedToDate = $request->toDate;

    
// dd( $selectedToDate, $selectedFromDate);
        $productsCount = KotItem::where('restaurant_id', $user->restaurant_id)
        ->where(function ($query) use ($selectedFromDate, $selectedToDate) {
            $query->whereBetween('.created_at', [$selectedFromDate, $selectedToDate])
                ->orWhereDate('.created_at', $selectedFromDate)
                ->orWhereDate('.created_at', $selectedToDate);
        })
        ->where('is_cancelled',0)
        ->where('status', 'COMPLETED')
        ->select('name', 'quantity','price','product_total','is_cancelled','created_at')
        ->get();

        // $totalProductsCount = $productsCount->sum('quantity');
        // dd($productsCount);

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

    public function itemtotalreport(Request $request)
    {
        // return "hii";
         //
        $user = Auth::user();
        $selectedFromDate = $request->fromDate;
        $selectedToDate = $request->toDate;

        // return $user;


        $productsGrouped = KotItem::where('restaurant_id', $user->restaurant_id)//$user->restaurant_id)
        ->where(function ($query) use ($selectedFromDate, $selectedToDate) {
            $query->whereBetween('.created_at', [$selectedFromDate, $selectedToDate])
                ->orWhereDate('.created_at', $selectedFromDate)
                ->orWhereDate('.created_at', $selectedToDate);
        })
            ->select('item_id', 'name', 'quantity', 'price', 'product_total','created_at')
            ->groupBy('item_id', 'name', 'quantity', 'price', 'product_total','created_at')
            ->get()
            ->groupBy('item_id')
            ->map(function ($groupedItems) {
                return $groupedItems->reduce(function ($carry, $item) {
                    $carry['item_id'] = $item['item_id'];
                    $carry['name'] = $item['name'];
                    $carry['quantity'] = ($carry['quantity'] ?? 0) + $item['quantity'];
                    $carry['product_total'] = ($carry['product_total'] ?? 0) + $item['product_total'];
                    $carry['price'] = $item['price']; 
                    $carry['created_at'] = $item['created_at']; // Keeping the price intact
                    return $carry;
                }, []);
            });

            $resultArray = $productsGrouped->values()->toArray();

            $restaurantName = Restaurant::where('id', $user->restaurant_id)->value('restaurant_name');



    return response()->json([
    // "success" => true,
    "restaurantName" => $restaurantName,
    "productsData" => $resultArray,
   
    // 'totalSale' => $totalSale
    
]);

    }
}
