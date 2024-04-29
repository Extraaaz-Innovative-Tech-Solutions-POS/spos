<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KOT;
use App\Models\KotItem;
use App\Models\Order;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DaySummaryReport extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedFromDate = $request->fromDate;
        $selectedToDate = $request->toDate;
    
        $results = Order::join('order_payments', 'orders.table_id', '=', 'order_payments.table_id')
        ->join('kot','kot.table_id','=','orders.table_id')
            ->where('orders.restaurant_id',$user->restaurant_id)
            ->where(function ($query) use ($selectedFromDate, $selectedToDate) {
                $query->whereBetween('orders.created_at', [$selectedFromDate, $selectedToDate])
                    ->orWhereDate('orders.created_at', $selectedFromDate)
                    ->orWhereDate('orders.created_at', $selectedToDate);
            })
            ->select('kot.order_number as bill_number',
               'orders.table_id','orders.order_type','orders.total','orders.total_discount','order_payments.payment_type','order_payments.created_at', 
            )
            ->groupBy('kot.order_number','orders.order_type','orders.table_id','orders.total','orders.total_discount','order_payments.payment_type','order_payments.created_at')
            ->get();
            // dd($results);
        // $payments = DB::table('order_payments')
        //     ->where('restaurant_id', $restaurant_id)
        //     ->where('location_id', $endDay_location_id)
        //     ->where('order_payments.created_at', '>=', $newdate)
        //     ->where('order_payments.created_at', '<', $dayAfter)
        //     ->where('paymentMode', $endDayLocationType)
        //     ->select(
        //         'paymentMode',
        //         DB::raw('COUNT(*) as cash_payment_count'),
        //         DB::raw('SUM(billamount) as total_cash_amount')
        //     )
        //     ->groupBy('paymentMode')
        //     ->get();
        return response()->json([
            "success" => true, "result" => $results
        ]);
      

       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cashierReport(Request $request)
    {
        $user = Auth::user();
        $selectedFromDate = $request->fromDate;
        $selectedToDate = $request->toDate;
   
        $totalInvoice = OrderPayment::where('restaurant_id', $user->restaurant_id)
        ->where(function ($query) use ($selectedFromDate, $selectedToDate) {
            $query->whereBetween('created_at', [$selectedFromDate, $selectedToDate])
                ->orWhereDate('created_at', $selectedFromDate)
                ->orWhereDate('created_at', $selectedToDate);
        })        ->where('status','COMPLETED')
        ->count();
        $totalSale = OrderPayment::where('restaurant_id', $user->restaurant_id)
        ->where(function ($query) use ($selectedFromDate, $selectedToDate) {
            $query->whereBetween('created_at', [$selectedFromDate, $selectedToDate])
                ->orWhereDate('created_at', $selectedFromDate)
                ->orWhereDate('created_at', $selectedToDate);
        })        ->where('status','COMPLETED')
        ->sum('amount');
        $productsCount = KotItem::where('restaurant_id', $user->restaurant_id)
        ->where(function ($query) use ($selectedFromDate, $selectedToDate) {
            $query->whereBetween('created_at', [$selectedFromDate, $selectedToDate])
                ->orWhereDate('created_at', $selectedFromDate)
                ->orWhereDate('created_at', $selectedToDate);
        })        ->where('status','COMPLETED')
        ->count();
        return response()->json(["success" => true, "totalInvoice" => $totalInvoice,'totalSale'=>$totalSale,'productsCount' => $productsCount
            ]);
    }
    public function cancelOrderReport(Request $request)
    {
        $user = Auth::user();
        $selectedFromDate = $request->fromDate;
        $selectedToDate = $request->toDate;

        $orders = KotItem::where('restaurant_id', $user->restaurant_id)
        ->where(function ($query) use ($selectedFromDate, $selectedToDate) {
            $query->whereBetween('created_at', [$selectedFromDate, $selectedToDate])
                ->orWhereDate('created_at', $selectedFromDate)
                ->orWhereDate('created_at', $selectedToDate);
        })
        ->where('is_cancelled', 1)
        ->get();

        return response()->json([
            "success" => true, "orders" => $orders
        ]);
      
    }
    public function cancelItemsReport(Request $request)
    {
        $user = Auth::user();
        $selectedFromDate = $request->fromDate;
        $selectedToDate = $request->toDate;

        $orders = KOT::join('kot_items', 'kot.table_id', '=', 'kot_items.table_id')
        ->where(function ($query) use ($selectedFromDate, $selectedToDate) {
            $query->whereBetween('created_at', [$selectedFromDate, $selectedToDate])
                ->orWhereDate('created_at', $selectedFromDate)
                ->orWhereDate('created_at', $selectedToDate);
        })
        ->where('kot_items.restaurant_id', $user->restaurant_id)
        ->whereBetween('kot_items.created_at', [$selectedFromDate, $selectedToDate])
        ->where('kot_items.is_cancelled', 1)
        ->get();

        return response()->json([
            "success" => true, "orders" => $orders
        ]);
      
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
