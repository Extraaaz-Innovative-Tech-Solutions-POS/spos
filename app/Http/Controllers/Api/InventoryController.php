<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Retrieve restaurant ID from the authenticated user if not passed in the request
        $restaurant_id = $request->input('restaurant_id', $user->restaurant_id);

        try {
            // Retrieve purchase orders (ingredients) for the given restaurant
            $purchase_orders = Ingredient::where('restaurant_id', $restaurant_id)
                ->select('id', 'product_name', 'quantity', 'unit')
                ->get();

            // Prepare JSON response
            return response()->json([
                'success' => true,
                'data' => [
                    'restaurant_id' => $restaurant_id,
                    'purchase_orders' => $purchase_orders,
                ]
            ]);
        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch inventory items',
                'error' => $e->getMessage(),
            ], 500);
        }
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
       // Validate the incoming request data
    $validator = Validator::make($request->all(), [
        // 'product_name' => 'required|string',
        'quantity' => 'required|numeric',
        'unit' => 'required|string',
        'reason'=> 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()->all()], 422);
    }

    try {
        // Retrieve the Ingredient record to update
        $ingredient = Ingredient::findOrFail($id);

        // Update the Ingredient record
        // $ingredient->product_name = $request->input('product_name');
        $ingredient->quantity = $request->input('quantity');
        $ingredient->unit = $request->input('unit');
        $ingredient->reason = $request->input('reason');

        $ingredient->save();

        // Prepare the updated data for response
        $updatedIngredient = [
            'id' => $ingredient->id,
            'product_name' => $ingredient->product_name,
            'quantity' => $ingredient->quantity,
            'unit' => $ingredient->unit,
            'reason' => $ingredient->reason,
        ];

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Inventory item updated successfully!',
            'data' => $updatedIngredient,
        ]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // Return error response if Ingredient with given ID is not found
        return response()->json([
            'success' => false,
            'message' => 'Inventory item not found',
            'error' => $e->getMessage(),
        ], 404);
    } catch (\Exception $e) {
        // Return error response if update fails for other reasons
        return response()->json([
            'success' => false,
            'message' => 'Failed to update inventory item',
            'error' => $e->getMessage(),
        ], 500);
    }
    // Implement other methods (store, show, destroy) as per your application's requirements
}
}
