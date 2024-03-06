<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        // return $request->all();
        $validated = $request->validate([
            'resturants_id' => 'required',
            'email' => 'required|email|unique:user',
            'password' => 'required',
            'phone' => 'required|numeric',
            // 'state' => 'required',
            'role' => 'required',
            'name' => 'required',
        ]);
        
        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);
        $success['token'] = $user->createToken('auth')->plainTextToken;
        $success['name'] = $user->name;
        // $success['customer_id'] = $user->id;
        $response = [
            'success' => true,
            'data' => $success,
            'message' => 'user register successfully'
        ];
        return response()->json($response, 200);
    }


public function login(Request $request)
{
    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        $user = Auth::user();
        $token = $user->createToken('auth')->plainTextToken;
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'state' => $user->state,
            'email' => $user->email, 
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
