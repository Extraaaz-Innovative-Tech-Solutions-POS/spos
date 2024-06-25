<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventorWastageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)


    {
        //
          // Get the authenticated user
    $user = Auth::user();
    $restaurant_id = $user->restaurant_id;

    // Retrieve the date range from the request
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');

    $inventoryAdjustmentsQuery = InventoryAdjustment::where('restaurant_id', $restaurant_id);

    // Apply date range filtering if both dates are provided
    if ($fromDate && $toDate) {
        $inventoryAdjustmentsQuery->where(function ($query) use ($fromDate, $toDate) {
            $query->whereBetween('created_at', [$fromDate, $toDate])
                ->orWhereDate('created_at', $fromDate)
                ->orWhereDate('created_at', $toDate);
        });
    }

    // Get the filtered inventory adjustments
    $inventoryAdjustments = $inventoryAdjustmentsQuery->latest()->get();

    // Transform the collection into a resource
    // $inventoryAdjustments = InventoryAdjustmentResource::collection($inventoryAdjustments);

    return response()->json([
        'success' => true,
        'data' => $inventoryAdjustments
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
