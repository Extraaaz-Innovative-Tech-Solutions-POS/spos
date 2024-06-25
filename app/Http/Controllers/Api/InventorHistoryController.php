<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InventorHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $restaurant_id = $request->input('restaurant_id', $user->restaurant_id);

        // Validate date inputs
        $validator = Validator::make($request->all(), [
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 422);
        }

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        // Query to retrieve inventory history
        $query = InventoryHistory::where('restaurant_id', $restaurant_id);

        // Apply date filters if provided
        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        } elseif ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        // Retrieve filtered inventory history
        $inventory_history = $query->get();

        // Prepare JSON response
        return response()->json([
            'success' => true,
            'data' => $inventory_history,
        ]);
    }
}
