<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
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
        
        $resturants_id = $request->input('restaurant_id');
        $data1 = User::where('id', $user->id)->get()->toArray(); // Corrected $user->Id to $user->id
        
        $cart= Cart::where('restaurant_id',$user->restaurant_id)->get();
        
        return response()->json(["success" => true, "data" =>$cart]);
    


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
        $restaurant_id = $user->restaurant_id;
        $cart = new Cart();
        // $cart->id = $request->id;
        $cart->table_number = $request->table_number;
        $cart->floor_number  = $request->floor_number;
        $cart->order_type = $request->order_type;
        $cart->customer_id = $request->customer_id;
        $cart->kot_id = $request->kot_id;
        // $cart->order_type = $request->order_type;
        // $cart->order_type = $request->order_type;

        $cart->save();
        return response()->json(['success' => true, 'message' => 'cart added successfully', 'data' => $cart]);


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
        $validatedData = $request->validate([
            // 'id' => 'id|string|max:255',
            'table_number' => 'required|string|max:255',
            'floor_number' => 'required|string|max:255',
            'order_type' => 'required|string|max:255',
            'customer_id' => 'required|string|max:255',
            'kot_id' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $cart = Cart::find($id);
        $cart->table_number = $request->table_number;
        $cart->floor_number  = $request->floor_number;
        $cart->order_type = $request->order_type;
        $cart->customer_id = $request->customer_id;
        $cart->kot_id = $request->kot_id;
        $cart->save();

        return response()->json(['success' => true, 'message' => 'cart updated successfully','data'=>$cart]);

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
