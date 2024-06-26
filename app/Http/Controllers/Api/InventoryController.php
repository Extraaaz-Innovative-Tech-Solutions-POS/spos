<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
// use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //

        
        $user = $request->user();
        $restaurant_id = $request->input('restaurant_id', $user->restaurant_id);

        try {
           
            $purchase_orders = Ingredient::where('restaurant_id', $restaurant_id)
                ->select('id', 'product_name', 'quantity', 'unit')
                ->get();

        
            return response()->json([
                'success' => true,
                'data' => [
                    'restaurant_id' => $restaurant_id,
                    'purchase_orders' => $purchase_orders,
                ]
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch inventory items',
                'error' => $e->getMessage(),
            ], 500);
        }
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
      
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric',
            'unit' => 'required|string',
            'reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 422);
        }

        try {
           
            $ingredient = Ingredient::findOrFail($id);

          
            $ingredient->quantity = $request->input('quantity');
            $ingredient->unit = $request->input('unit');
            $ingredient->reason = $request->input('reason');

            $ingredient->save();

           
            $updatedIngredient = [
                'id' => $ingredient->id,
                'product_name' => $ingredient->product_name,
                'quantity' => $ingredient->quantity,
                'unit' => $ingredient->unit,
                'reason' => $ingredient->reason,
            ];

         
            return response()->json([
                'success' => true,
                'message' => 'Inventory item updated successfully!',
                'data' => $updatedIngredient,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
           
            return response()->json([
                'success' => false,
                'message' => 'Inventory item not found',
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
           
            return response()->json([
                'success' => false,
                'message' => 'Failed to update inventory item',
                'error' => $e->getMessage(),
            ], 500);
        }
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
