<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KotItem;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Restaurant;
use App\Models\Restaurants;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashierHallWiseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $restaurant_id = $request->input('restaurant_id');
        $fromDate = $request->fromDate; // Assuming date is passed in the request, adjust as needed
        $toDate = Carbon::parse($request->toDate)->addDay()->format('Y-m-d'); 
        // Fetching user data
        $userData = User::find($user->id);
        
        $results = Order::join('order_payments', 'orders.table_id', '=', 'order_payments.table_id')
        ->where('orders.restaurant_id', $user->restaurant_id)
        ->whereBetween('orders.created_at', [$fromDate, $toDate])
        ->select(
            'orders.order_type','orders.table_id',
         
            DB::raw('order_payments.amount as total_bill_amount')
        )
        ->distinct()
        ->groupBy('orders.order_type','total_bill_amount','orders.table_id',)
        ->get();
        
        // Fetching restaurant name based on restaurant_id
        $restaurantName = Restaurant::where('id', $user->restaurant_id)->value('restaurant_name');
       
        
        return response()->json([
            "success" => true,
            "data" => [
                "restaurant_name" => $restaurantName,
                "orders" => $results
                
            ]
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
