<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Items;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemsController extends Controller
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
        
        $resturants_id  = $request->input('restaurant_id ');
        $data1 = User::where('id', $user->id)->get()->toArray(); // Corrected $user->Id to $user->id
        
        $items = Items::where('restaurant_id ', $resturants_id)
                    
                    ->get();
        
        return response()->json(["success" => true, "data" => $items]);


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

        $user = Auth::user();
        // $data1 = User::where('id', $user->id)->get()->toArray(); // Corrected $user->Id to $user->id
        $restaurant_id = $user->resturants_id;
        $items = new Items();
        $items->item_id  = $request->item_id  ;
        $items->item_name = $request->item_name;
        $items->price  = $request->price ;
        $items->discount = $request->discount;
        $items->category_id  = $request->category_id ;
        $items->food_type = $request->food_type;
        $items->inventory_status = $request->inventory_status;
        $items->associated_item = $request->associated_item;
        $items->varients = $request->varients;
        $items->tax_percentage = $request->tax_percentage;
        // $items->discount = $request->discount;

        $items->restaurant_id = $restaurant_id;
        $items->save();
        return response()->json(['success' => true, 'message' => 'items added successfully', 'data' => $items]);

        

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
