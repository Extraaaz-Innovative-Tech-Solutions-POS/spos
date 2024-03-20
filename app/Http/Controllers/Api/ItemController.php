<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
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
        
        $restaurant_id   = $request->input('restaurant_id');
        $data1 = User::where('id', $user->id)->get()->toArray(); // Corrected $user->Id to $user->id
        
         
        $Item = Item::where('restaurant_id', $user->restaurant_id)
                    ->get();

        $Item = ItemResource::collection($Item);
        
        return response()->json(["success" => true, "data" => $Item]);


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        // $data1 = User::where('id', $user->id)->get()->toArray(); // Corrected $user->Id to $user->id
        $restaurant_id = $user->restaurant_id;

        $Item = new Item();
        // $Item->item_id  = $request->item_id;
        $Item->item_name = $request->item_name;
        $Item->restaurant_id = $restaurant_id;
        $Item->price  = $request->price ? $request->price : null;
        $Item->discount = $request->discount ? $request->discount : null;
        $Item->category_id  = $request->category_id ? $request->category_id : null;
        $Item->food_type = $request->food_type ? $request->food_type : null;
        $Item->inventory_status = $request->inventory_status ? $request->inventory_status : null;
        $Item->associated_item = $request->associated_item ? $request->associated_item : null;
        $Item->varients = $request->varients ? $request->varients : null;
        $Item->tax_percentage = $request->tax_percentage ? $request->tax_percentage : null;
        $Item->save();

        return response()->json(['success' => true, 'message' => 'Item saved successfully', 'data' => $Item]);
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
        $validatedData = $request->validate([
            // 'item_id' => 'required|string|max:255',
            'item_name' => 'required|string|max:255',
            // 'price' => 'required|string|max:255',
            // 'discount' => 'required|string|max:255',
            // 'category_id' => 'required|string|max:255',
            // 'food_type' => 'string|max:255',
            // 'inventory_status' => 'string|max:255',
            // 'associated_item' => 'string|max:255',
            // 'varients' => 'string|max:255',
            // 'tax_percentage' => 'string|max:255',
            // 'varients' => 'string|max:255',
        ]);

        $user = Auth::user();

        $Item = Item::findOrFail($id);
        $Item->item_name = $request->item_name;
        $Item->restaurant_id = $user->restaurant_id;
        $Item->price  = $request->price ? $request->price : null;
        $Item->discount = $request->discount ? $request->discount : null;
        $Item->category_id  = $request->category_id ? $request->category_id : null;
        $Item->food_type = $request->food_type ? $request->food_type : null;
        $Item->inventory_status = $request->inventory_status ? $request->inventory_status : null;
        $Item->associated_item = $request->associated_item ? $request->associated_item : null;
        $Item->varients = $request->varients ? $request->varients : null;
        $Item->tax_percentage = $request->tax_percentage ? $request->tax_percentage : null;
        $Item->save();
        return response()->json(['success' => true, 'message' => 'Category updated successfully','data'=>$Item]); //, 'data' => $category]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Item::findorFail($id);
        $name = $item->item_name;
        $item->delete();
        return response()->json(["success" => true, "message" => $name . ' Item is Deleted Successfully']);
    }

    public function checkModifiers($item_id)
    {


    }
}