<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $restaurant_id = $user->restaurant_id;
        $role = $user->role; 

        if($role == "manager"){
            $users = User::where('restaurant_id',$restaurant_id)->get();
        }
        else{
            return response()->json(['success'=>false, 'message'=>'Unauthorized']);
        }

        return response()->json(['success'=>true, 'message' => 'Staff Data', 'data'=>$users]);
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
        $restaurant_id = $user->restaurant_id;

        $staff = new User();
        $staff->restaurant_id = $restaurant_id;
        $staff->name = $request->name;
        $staff->email = $request->email;
        $staff->phone = $request->phone;
        $staff->password = bcrypt($request->password);
        $staff->role = $request->role;
        $staff->save();

        return response()->json(["success"=> true, "message"=>"Staff Data Saved Successfully", "data" => $staff]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = User::findOrFail($id);
        return response()->json(["success"=>true, "message"=>"Staff Data", "data"=>$data]);
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
        $staff = User::findOrFail($id);
        $staff->restaurant_id = $restaurant_id;
        $staff->name = $request->name;
        $staff->phone = $request->phone;
        $staff->password = bcrypt($request->password);
        $staff->role = $request->role;
        $staff->save();
        return response()->json(["success" => true, "message" => "Staff Data update Saved Successfully", "data" => $staff]);

        
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
        $user = Auth::user();
        $role = $user->role;

        if ($role != "manager") {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $staff = User::findOrFail($id);
        $staff->delete();

        return response()->json(["success" => true, "message" => "Staff Data Deleted Successfully"]);

       
    }
}
