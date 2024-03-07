<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Floor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FloorController extends Controller
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
        
        $restaurant_id  = $request->input('restaurant_id');

        $data1 = User::where('id', $user->id)->get()->toArray(); // Corrected $user->Id to $user->id
        
        $floor = Floor::where('restaurant_id', $restaurant_id)->get();
        
        return response()->json(["success" => true, "data" => $floor]);

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
          //
          $user = Auth::user();
          $restaurant_id = $user->restaurant_id;
          $floor = new floor();
          $floor->id  = $request->id ;
          $floor->restaurant_id = $request->restaurant_id;
          $floor->floor  = $request->floor ;
        //   $floor->descirption = $request->descirption;
        //   $floor->restaurant_id = $restaurant_id;
          $floor->save();
          return response()->json(['success' => true, 'message' => 'floor added successfully', 'data' => $floor]);

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
        $floor = User::findOrfail($id);
        return response ()->json(["success" => true, "message"=>"show floor","data" => $floor]);


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
        $floor = floor::findorFail($id);
        $name = $floor->floor_name;
        $floor->delete();
        return response()->json(["success" => true, "message" => $name . ' floor is Deleted Successfully']);
    }
}
