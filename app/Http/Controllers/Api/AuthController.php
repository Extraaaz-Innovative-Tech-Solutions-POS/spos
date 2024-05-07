<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //REGISTER
    public function register(Request $request)
    {
        //    $count = User::count() + 1;
        $validated = $request->validate([
            'email' => 'required|email|unique:user',
            'password' => 'required',
            'phone' => 'required|numeric',
            'role' => 'required',
            'name' => 'required',
            'type' => 'required',


        ]);

        $validated['password'] = bcrypt($validated['password']);
        //    $validated['restaurant_id'] = $count;
        $validated['plain_password']= $request['password'];

        //    $user = User::create($validated);
        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = $validated['password'];
        $user->plain_password = $validated['plain_password'];
        $user->role = $validated['role'];
        $user->phone = $validated['phone'];
        $user->business_type =$validated['type'];

        $user->save();

        $restaurant = new Restaurant();
        $restaurant->user_id = $user->id;
        $restaurant->name = $request->restaurant_name;
        $restaurant->email = $request->restaurnt_email;
        $restaurant->phone = $request->restaurant_phone;
        $restaurant->address = $request->address;
        $restaurant->city = $request->city;
        // $restaurant->district = $request->district;
        $restaurant->state = $request->state;
        $restaurant->country = $request->country ?? 'India';
        $restaurant->pincode = $request->pincode;
        $restaurant->license_id = $request->license_id ?? null;
        $restaurant->fssai_id = $request->fssai_id ?? null;
        $restaurant->gst_no = $request->gst_no ?? null;
        // $restaurant->latitude = $request->latitude ?? null;
        // $restaurant->longitude = $request->longitude ?? null;
        $restaurant->save();

        $user->restaurant_id = $restaurant->id;
        $user->save();

        $success['token'] = $user->createToken('auth')->plainTextToken;
        $success['name'] = $user->name;
        $success['status'] = 'Inactive';
        //    $success['restaurant_id'] = $count;
        $success['role'] = $user->role;

        $response = [
            'success' => true,
            'data' => $success,
            'message' => 'User & Restaurant Registered Successfully',
        ];

        return response()->json($response, 200);
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'status' => 'Active'])) {
            $user = Auth::user();
            $token = $user->createToken('auth')->plainTextToken;
            
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'state' => $user->state,
                'email' => $user->email,
                'role' => $user->role,
                'type' => $user->business_type,
            ];

            $restaurant = $user->restaurant;//->map->only([ 'name']);

            $response = [
                'success' => true,
                'data' => ['token' => $token, 'user' => $userData , 'restaurant' => $restaurant],
                'message' => 'User logged in successfully',
            ];

            return response()->json($response, 200);
        } else {
            $response = [
                'success' => false,
                'message' => 'Unauthorized',
            ];
            return response()->json($response, 401);
        }
    }

    public function me(Request $request)
    {
    }

    public function updateProfile(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'role' => 'required',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->phone = $request->phone;
        // $user->plain_password = $request->password;
        // $user->password = bcrypt($request->password);
        // $user->state = $request->state;
        $user->email = $request->email;
        $user->role = $request->role;
        // $user->business_type = $request->business_type;
        $user->save();

        return response()->json(["success" => true, "user" => $user, "message" => "Updated profile successfully"]);
    }

    public function logout(Request $request)
    {
    }

    public function updateRestaurant(Request $request, $id)
    {
        $validated = $request->validate([
            'restaurant_name' => 'required',
            'restaurant_email' => '',
            'restaurant_phone' => '',
            'address' => '',
            'city' => '',
            'state' => '',
            'country' => '',
            'pincode' => '',
            'license_id' => '',
            'fssai_id' => '',
            'gst_no' => '',
            'latitude' => '',
            'longitude' => '',
        ]);

        $restaurant = Restaurant::findOrFail($id);
        $restaurant->name = $request->restaurant_name;
        $restaurant->email = $request->restaurant_email;
        $restaurant->phone = $request->restaurant_phone;
        $restaurant->address = $request->address;
        $restaurant->city = $request->city;
        // $restaurant->district = $request->district;
        $restaurant->state = $request->state;
        $restaurant->country = $request->country;
        $restaurant->pincode = $request->pincode;
        $restaurant->license_id = $request->license_id;
        $restaurant->fssai_id = $request->fssai_id;
        $restaurant->gst_no = $request->gst_no;
        // $restaurant->latitude = $request->latitude;
        // $restaurant->longitude = $request->longitude;
        $restaurant->save();

        return response()->json(["success" => true, "data" => $restaurant, "message" => "Restaurant Updated Successfully"]);
    }

}
