<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaxController extends Controller
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
        
        $restaurant_id   = $request->input('restaurant_id');
        $data1 = User::where('id', $user->id)->get()->toArray(); // Corrected $user->Id to $user->id
        
        $tax = Tax::where('restaurant_id', $restaurant_id )
                    ->get();
        
        return response()->json(["success" => true, "data" => $tax]);
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
        $tax = new Tax();
        $tax->id   = $request->id;
        $tax->name = $request->name;
        // $tax->restaurant_id  = $request->restaurant_id ;
        // $tax->descirption = $request->descirption;
        $tax->restaurant_id = $restaurant_id;
        $tax->save();
        return response()->json(['success' => true, 'message' => 'tax added successfully', 'data' => $tax]);

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
        $user = Auth::user();
        $tax = tax::find($id);
        $validatedData = $request->validate([
            'id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            // 'descirption' => 'required|string|max:255',
       



        ]);
        $tax->update($validatedData);
        return response()->json(['success' => true, 'message' => 'tax updated successfully','data'=>$tax]);
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
