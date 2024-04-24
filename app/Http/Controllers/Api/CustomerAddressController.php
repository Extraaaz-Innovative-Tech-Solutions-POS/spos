<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $customerAddresses = CustomerAddress::where('restaurant_id',$user->restaurant_id)->get();

        return response()->json(["success" => true, "data" => $customerAddresses]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required',
        ]);

        $user = Auth::user();
        $restaurant_id = $user->restaurant_id;
        $customer_id = $request->customer_id;

        $customer = new CustomerAddress();
        $customer->user_id   = $user->id;
        $customer->customer_id = $customer_id;
        $customer->type = $request->type;
        $customer->address  = $request->address;
        $customer->city = $request->city;
        $customer->state = $request->state;
        $customer->country = $request->country;
        $customer->pincode = $request->pincode;
        $customer->restaurant_id = $restaurant_id;
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
        $data = CustomerAddress::withTrashed()->findOrFail($id);
        if (!$data) {
            return response()->json(["success" => false, "message" => 'Customer Address not found'], 404);
        }
        return response()->json(['success' => true, 'message' => 'Customer Address retrieved successfully', 'data' => $data]);
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
        $customer_address = CustomerAddress::find($id);

        if (!$customer_address) {
            return response()->json(["success"=> false ,"message" => 'Customer Address not found'],404);
        }

        $user = Auth::user();
        $restaurant_id = $user->restaurant_id;

        $customer_address->type = $request->type;
        $customer_address->address  = $request->address;
        $customer_address->city = $request->city;
        $customer_address->state = $request->state;
        $customer_address->country = $request->country;
        $customer_address->pincode = $request->pincode;
        // $customer_address->restaurant_id = $restaurant_id;
        $customer_address->save();

        return response()->json(['success' => true, 'message' => 'Customer Address update successfully', 'data' => $customer_address]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer_address = CustomerAddress::find($id);
        if (!$customer_address) {
            return response()->json(["success" => false, "message" => 'Customer Address not found'], 404);
        }
        $customer_address->delete();
        return response()->json(["success" => true , "message" => 'Customer Address deleted successfully']);
    }

    public function getCustomerAddresses($customerId)
    {
        $customer = Customer::findOrFail($customerId);
        if (!$customer) {
            return response()->json(["success" => false, "message" => 'Customer Address not found'], 404);
        }
        $customerAddresses =CustomerAddress::where('customer_id', $customer->id)->get(); //  $customer->customer_addresses;
        return response()->json(["success" =>true ,"data"=>$customerAddresses,"message" => 'Addresses of ' . $customer->name]); // response()->json($customerAddresses);
    }
}
