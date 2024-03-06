<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tables;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TablesController extends Controller
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
        $resturants_id  = $request->input('restaurant_id');
        $data1 = User::where('id', $user->id)->get()->toArray(); // Corrected $user->Id to $user->id
        
        $table = Tables::where('restaurant_id', $resturants_id)->get();

  return response()->json(["success" => true, "data" => $table]);


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
        // $data1 = User::where('id', $user->id)->get()->toArray(); // Corrected $user->Id to $user->id
        $restaurant_id = $user->restaurant_id;
        $table = new Tables();
        $table->floor_id  = $request->floor_id ;
        $table->restaurant_id = $request->restaurant_id;
        $table->floor_number  = $request->floor_number ;
        $table->tables = $request->tables;
        $table->save();
        return response()->json(['success' => true, 'message' => 'table added successfully', 'data' => $table]);
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
    }
}
