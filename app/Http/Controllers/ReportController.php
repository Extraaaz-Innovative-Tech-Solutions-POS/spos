<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function customerSaleReport(Request $request)
    {

        $user = Auth::user();

        // return $user;


        $customerDate = $request->customer_date;
        $customerToDate = $request->customer_to_date;

        // dd($user->restaurant_id);

        // $results = Order::join('order_payments', 'orders.table_id', '=', 'order_payments.table_id')
        // ->where('orders.restaurant_id', '=', $user->restaurant_id) // Explicitly specify collation
        // ->whereBetween('orders.created_at', [$customerDate, $customerToDate])
        // ->join('customer', 'orders.customer_id', '=', 'customer.id')
        // ->select('orders.table_id', 'orders.customer_id', 'order_payments.amount', 'customer.name')
        // ->groupBy('orders.table_id', 'orders.customer_id', 'order_payments.amount', 'customer.name')
        // ->get()->toArray();


        $results = Order::leftJoin('order_payments', 'orders.table_id', '=', 'order_payments.table_id')
            
            ->leftJoin('customer', 'orders.customer_id', '=', 'customer.id')
            ->where('orders.restaurant_id', '=', $user->restaurant_id)
            ->whereBetween('orders.created_at', [$customerDate, $customerToDate])
            ->select('orders.table_id', 'orders.customer_id', 'order_payments.amount', 'customer.name')
            ->groupBy('orders.table_id', 'orders.customer_id', 'order_payments.amount', 'customer.name')
            ->get();

        // return ($results);

        $resultArray = [];

        // Iterate through the input array
        foreach ($results as $item) {
            $customerId = $item["customer_id"];
            $billAmount = floatval($item["amount"]);
            if (isset($resultArray[$customerId])) {
                $resultArray[$customerId]["purchase_count"]++;
                $resultArray[$customerId]["total_billamount"] += $billAmount;
            } else {
                $resultArray[$customerId] = [
                    "customer_id" => $customerId,
                    "name" => $item["name"],
                    "purchase_count" => 1,
                    "total_billamount" => $billAmount,
                ];
            }
        }
        // dd(array_values($resultArray));

        $results = array_values($resultArray);



        return response()->json([
            "success" => true, "result" => $results
        ]);
    }
}
