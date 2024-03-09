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
        
        $cart= Cart::where('restaurant_id',$resturants_id)->get();
        
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
        $customer = new Cart();
        $customer->id   = $request->id;
        $customer->name = $request->name;
        $customer->address  = $request->address ;
        $customer->phone = $request->phone;
        $customer->restaurant_id = $restaurant_id;
        $customer->save();
        return response()->json(['success' => true, 'message' => 'customer added successfully', 'data' => $customer]);


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
