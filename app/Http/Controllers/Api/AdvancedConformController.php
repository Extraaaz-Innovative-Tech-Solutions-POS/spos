<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KOT;
use App\Models\KotItem;
use App\Models\Section;
use App\Models\TableActive;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdvancedConformController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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


    public function confirmAdvOrder(Request $request)
{
    $user = Auth::user();
    $request->validate([
        'table_id' => 'required',
        'items' => 'required',
        'orderType' => 'required',
        'customerId' => '',
    ]);

    return DB::transaction(function () use ($user, $request) {

        $oldKot = KOT::where('restaurant_id', $user->restaurant_id)
            ->where('table_id', $request->table_id)
            ->first();
        if ($oldKot) {
            return response()->json(['success' => false, 'message' => 'Table already has an order with the same table_id']);
        }

        $todaysDate = now()->toDateString(); // Get today's date in 'Y-m-d' format
        $order_number = 1;

        $table_order_number = KOT::where('restaurant_id', $user->restaurant_id)
            ->whereDate('created_at', $todaysDate)
            ->latest()
            ->pluck('order_number')
            ->first();

        if ($table_order_number) {
            $order_number = $table_order_number + 1;
        }

        $advanceDate = $request->advance_order_date_time;
        $advanceDate = $advanceDate ? Carbon::createFromFormat('Y-m-d h:i A', $advanceDate) : null;

        $kot = new KOT();
        $kot->table_id = $request->table_id;
        $kot->order_number = $order_number;
        $kot->sub_table_number = $request->sub_table ?? null;
        $kot->section_id = $request->section_id ?? null;
        $kot->table_number = $request->table ?? null;
        $kot->floor_number = $request->floor ?? null;
        $kot->order_type = $request->orderType;
        $kot->customer_id = $request->customerId;
        $kot->restaurant_id = $user->restaurant_id;
        $kot->status = "PENDING";
        $kot->advance_order_date_time = $advanceDate; // $request->advance_order_date_time;
        $kot->save();

        $grand_total = 0;

        foreach ($request->items as $orderItem) {
            $kotItem = new KotItem();
            $kotItem->kot_id = $kot->id;
            $kotItem->table_id = $request->table_id;
            $kotItem->item_id = $orderItem['Id'];
            $kotItem->quantity = $orderItem['quantity'];
            $kotItem->price = $orderItem['price'];
            $kotItem->product_total = $orderItem['quantity'] * $orderItem['price'];
            $kotItem->name = $orderItem['name'];
            $kotItem->instruction = $orderItem['instruction'] ?? null;
            $kotItem->restaurant_id = $user->restaurant_id;
            $kotItem->save();

            $grand_total += $orderItem['quantity'] * $orderItem['price'];
        }

        $kot->total = $grand_total;
        $kot->grand_total = $grand_total;
        $kot->save();

        if ($request->orderType == "Dine") {
            $section_name = Section::where('id', $request->section_id)->first()->name;

            $tableActive = new TableActive();
            $tableActive->user_id = $user->id;
            $tableActive->table_id = $request->table_id;
            $tableActive->table_number = $request->table;
            $tableActive->divided_by = $request->table_divided_by ?? null;
            $tableActive->split_table_number = $request->sub_table ?? null;
            $tableActive->section_id = $request->section_id;
            $tableActive->section_name = $section_name ?? null;
            $tableActive->floor_number = $request->floor;
            $tableActive->restaurant_id = $user->restaurant_id;
            $tableActive->cover_count = $request->cover_count ?? null;
            $tableActive->status = "Occupied";
            $tableActive->save();
        }

        return response()->json(['success' => true, 'message' => 'Order confirmed successfully'], 200);
    });
}

}
