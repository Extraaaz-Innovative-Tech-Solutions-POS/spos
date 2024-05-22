<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GraphController extends Controller
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
        $restaurant_id = $request->input('restaurant_id');
        $fromDate = $request->fromDate; // Assuming date is passed in the request, adjust as needed
        $toDate = Carbon::parse($request->toDate)->addDay()->format('Y-m-d');
        // Fetching user data
        $userData = User::find($user->id);
        if ($request->input('order_type') === 'catering') {
            $data = DB::table('orders')
                ->join('order_payments', 'orders.table_id', '=', 'order_payments.table_id')
                ->where('orders.order_type', 'catering')
                ->select(DB::raw('DATE(orders.created_at) as date'), DB::raw('SUM(order_payments.money_given) as total_payment'))
                ->where('order_payments.status','COMPLETED')
                ->whereDate('orders.created_at', Carbon::today())
                ->groupBy(DB::raw('DATE(orders.created_at)'))
                ->get();
        } else {
            $data = OrderPayment::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total_payment'))
                ->where('restaurant_id', $user->restaurant_id)
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->groupBy(DB::raw('DATE(created_at)'))
                ->get();
        }   
        
            
        return response()->json([
            "success" => true, "result" => $data
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
