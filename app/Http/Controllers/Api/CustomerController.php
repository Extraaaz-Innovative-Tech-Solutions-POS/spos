<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
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
       


        // $location = Location::find($locationId);
        
        $data1 = User::where('id', $user->id)->get()->toArray(); // Corrected $user->Id to $user->id
        
        $customer= Customer::where('restaurant_id',$user->restaurant_id)->get();
        
        return response()->json(["success" => true, "data" =>$customer]);
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

        $customer = new Customer();

        // $customer->id   = $request->id;
        $customer->name = $request->name;
        $customer->address  = $request->address;
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
        $user = Auth::user();
        $restaurant_id = $user->restaurant_id;

        $customer = Customer::findOrFail($id);

        return response()->json(['success' => true, 'message' => 'Customer Data', 'data' => $customer]);
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
        $user = Auth::user();
        $restaurant_id = $user->restaurant_id;

        $customer = Customer::findOrFail($id);

        // $customer->id   = $request->id;
        $customer->name = $request->name;
        $customer->address  = $request->address;
        $customer->phone = $request->phone;
        $customer->restaurant_id = $restaurant_id;
        $customer->save();

        return response()->json(['success' => true, 'message' => 'customer added successfully', 'data' => $customer]);
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

        $customer = Customer::findOrFail($id);
        $customerName = $customer->name;
        
        $customer->delete();

        return response()->json(['success' => true, 'message' => 'customer deleted successfully', 'data' => $customerName]);
    }
}
