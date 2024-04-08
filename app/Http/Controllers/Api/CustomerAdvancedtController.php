<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAdvancedtController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        // $user = Auth::user();
            
        $customerAddresses = CustomerAddress::all();//where('restaurant_id',1)->get();// $user->restaurant_id)->get();
        
        return response()->json(["success" => true,"data" =>$customerAddresses]);

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
        $customer_id = $request->customer_id; 
        $customer_id = $user->id;
        // Assuming this comes from the request

        $customer = new CustomerAddress();
// $customer->id   = $request->id;
        $customer->type = $request->type;
        $customer->address  = $request->address;
        $customer->city = $request->city;
        $customer->state = $request->state;
        $customer->country = $request->country;
        $customer->pincode = $request->pincode;
        $customer->restaurant_id = $restaurant_id;
        $customer->customer_id = $customer_id;
        $customer->save();

        return response()->json(['success' => true, 'message' => 'customer address added successfully', 'data' => $customer]);
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
        $user = Auth::user();
        $restaurant_id = $user->restaurant_id;
        $customer_id = $user->id;

      $customer = CustomerAddress::findOrFail($id);
        $customer->type = $request->type;
        $customer->address  = $request->address;
        $customer->city = $request->city;
        $customer->state = $request->state;
        $customer->country = $request->country;
        $customer->pincode = $request->pincode;
        $customer->restaurant_id = $restaurant_id;
        $customer->save();

        return response()->json(['success' => true, 'message' => 'customer address update successfully', 'data' => $customer]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    
        $user = Auth::user();
        $restaurant_id = $user->restaurant_id;

        $customer = CustomerAddress::findOrFail($id);
        $customerName = $customer->type;
        
        $customer->delete();

        return response()->json(['success' => true, 'message' => 'customer address deleted successfully', 'data' => $customerName]);

    }
}
