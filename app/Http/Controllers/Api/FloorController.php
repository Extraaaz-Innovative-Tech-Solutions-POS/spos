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
        $user = Auth::user();

        $floor = Floor::where('restaurant_id',$user->restaurant_id)->get();
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
        $user = Auth::user();

        $floor = new Floor();
        $floor->restaurant_id = $user->restaurant_id;
        $floor->floor = $request->floor ;
        $floor->save();
        return response()->json(['success' => true, 'message' => 'Floor Added successfully', 'data' => $floor]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $floor = Floor::where("restaurant_id",$id)->first;

        if(!$floor)
        {
            return response()->json(['success' => false, 'message' =>"Floors Data not found for this restaurant_id"]);
        }
        return response ()->json(["success" => true, "message"=>"Floor data", "data" => $floor]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        
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
        $floor = floor::findOrFail($id);
        $name = $floor->floor_name;
        $floor->delete();

        return response()->json(["success" => true, "message" => $name . ' floor is Deleted Successfully']);
    }
}
