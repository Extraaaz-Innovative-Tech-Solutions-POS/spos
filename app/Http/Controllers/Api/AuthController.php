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
        ]);
       
        $validated['password'] = bcrypt($validated['password']);
        //    $validated['restaurant_id'] = $count;
        //    $validated['plain_password']= $request['password'];

        //    $user = User::create($validated);
        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = $validated['password'];
        $user->role = $validated['role'];
        $user->phone = $validated['phone'];
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
        $restaurant->country = $request->country ?? "India";
        $restaurant->pincode = $request->pincode;
        $restaurant->license_id = $request->license_id ?? null;
        $restaurant->fssai_id = $request->fssai_id ?? null;
        $restaurant->gst_no = $request->gst_no ?? null;
        $restaurant->latitude = $request->latitude ?? null;
        $restaurant->longitude = $request->longitude ?? null;
        $restaurant->save();

        $user->restaurant_id = $restaurant->id;
        $user->save();

        $success['token'] = $user->createToken('auth')->plainTextToken;
        $success['name'] = $user->name;
        $success['status'] = "Inactive";
        //    $success['restaurant_id'] = $count;
        $success['role'] = $user->role;

        $response = [
            'success' => true,
            'data' => $success,
            'message' => 'User Registered & Restaurant Successfully'
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

        $response = [
            'success' => true,
            'data' => ['token' => $token, 'user' => $userData],
            'message' => 'User logged in successfully'
        ];

        return response()->json($response, 200);
    } else {
        $response = [
            'success' => false,
            'message' => 'Unauthorized'
        ];
        return response()->json($response, 401);
    }
}
}
