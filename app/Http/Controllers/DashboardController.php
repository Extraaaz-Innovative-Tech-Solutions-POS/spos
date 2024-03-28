<?php

namespace App\Http\Controllers;

use App\Models\KOT;
use App\Models\KotItem;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function topSellingItems(Request $request)
    {
        $user = Auth::user();

        $data = KotItem::select('item_id')
            ->select('item_id', 'name', 'price' )
            ->where('restaurant_id', $user->restaurant_id)
            ->where('status', 'COMPLETED')
            ->groupBy('item_id', 'name', 'price')
            ->orderByRaw('COUNT(item_id) DESC')
            ->limit(10)
            ->get();


        return response()->json(["success" => true, "data" => $data]);
    }


    public function dashboardCards(Request $request)
    {
        $user = Auth::user();

        $currentDate = Carbon::now();


        $todaySales = Order::where('restaurant_id', $user->restaurant_id)
            ->whereDate('created_at', $currentDate)

            ->sum('total');


        $unsettledAmount = KOT::where('restaurant_id', $user->restaurant_id)
            ->whereDate('created_at', $currentDate)
            ->where('status', 'PENDING')
            ->sum('total');

        $todayOrderCount = Order::where('restaurant_id', $user->restaurant_id)
            ->whereDate('created_at', $currentDate)
            ->count();

        $todayinvoices = Order::where('restaurant_id', $user->restaurant_id)
            ->whereDate('created_at', $currentDate)
            ->count();

        $yesterdayDate = Carbon::yesterday()->toDateString();

        $yesterdaySaleTotal = Order::where('restaurant_id', $user->restaurant_id)
            ->whereDate('created_at', $yesterdayDate)
            ->sum('total');

            $currentMonthStart = Carbon::now()->startOfMonth(); // Get the start of the current month
            $currentMonthEnd = Carbon::now()->endOfMonth(); // Get the end of the current month
            
            $monthlyInvoices = Order::where('restaurant_id', $user->restaurant_id)
                ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
                ->count();
             
            $monthlyOrders = Order::where('restaurant_id', $user->restaurant_id)
                ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
                ->count();
                
            $monthlySales = Order::where('restaurant_id', $user->restaurant_id)
                ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
                ->sum('total');

            $totalSaleAmount = Order::where('restaurant_id', $user->restaurant_id)            
            ->sum('total');


        return response()->json([
            "success" => true, "Today Sales" => $todaySales,
            'Unsettled Amount' => $unsettledAmount, 'Today Orders Count' => $todayOrderCount,
            'Today\'s Invoices' => $todayinvoices, 'Yesterday\'s Sales Total' => $yesterdaySaleTotal,
            'Monthly Invoices'=>$monthlyInvoices,'Monthly Orders'=>$monthlyOrders,
            'Monthly Sales'=>$monthlySales,'Total Sales Amount'=>$totalSaleAmount

        ]);
    }

    public function cashPaymentAmount(Request $request)
    {
        $user = Auth::user();

        $data = Order::where('orders.restaurant_id', $user->restaurant_id)
        ->join('order_payments', 'orders.id', '=', 'order_payments.order_id')
        // ->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd])
        ->where('payment_type','CASH')
        ->sum('total');

        return response()->json(["success" => true, "data" => $data]);

    }

    public function onlinePaymentAmount(Request $request)
    {
        $user = Auth::user();

        $data = Order::where('orders.restaurant_id', $user->restaurant_id)
        ->join('order_payments', 'orders.id', '=', 'order_payments.order_id')
        ->where('payment_type','ONLINE')
        ->sum('total');

        return response()->json(["success" => true, "data" => $data]);

    }

}
