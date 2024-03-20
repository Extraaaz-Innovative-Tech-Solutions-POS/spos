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
        $floor = Floor::where('restaurant_id', $user->restaurant_id)->get();

        if(!$floor)
        {
                return response()->json(["success"=>false, "message"=>"Data does exists for this Restaurant"]);
        }
        return response()->json(['success' => true, 'data' => $floor]);
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

        $existing_floor = Floor::where('restaurant_id', $user->restaurant_id)->first();

        if ($existing_floor) {
            return response()->json(['success' => true, 'message' => 'Floor Data already exists for this Restaurant Id.']);
        }

        $floor = new Floor();
        $floor->restaurant_id = $user->restaurant_id;
        $floor->floor = $request->floor;
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
        $user = Auth::user();
        $floor = Floor::findOrFail($id);

        return response()->json(['success' => true, 'message' => 'Floor data', 'data' => $floor]);
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
        $user = Auth::user();

        $floor = Floor::where('restaurant_id', $user->restaurant_id)->first();
        $floor->floor = $request->floor;
        $floor->save();

        return response()->json(['success' => true, 'message' => 'Floor data Updated Successfully', 'data' => $floor]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $floor = Floor::findOrFail($id);
        // $name = $floor->floor;
        $floor->delete();

        return response()->json(['success' => true, 'message' => 'Floor is Deleted Successfully']);
    }
}
