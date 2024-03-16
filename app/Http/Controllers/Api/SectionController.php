<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
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

        $data1 = User::where('id', $user->id)->get()->toArray();

        $section = Section::where('restaurant_id', $user->restaurant_id)->get();

        return response()->json(["success" => true, "data" => $section]);
    
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
        $section = new section();
        $section->user_id   = $user->id;
        $section->name = $request->name;
        $section->restaurant_id  = $request->retaurant_id;
        // $section->floor_number = $request->floor_number;
        $section->save();
        return response()->json(['success' => true, 'message' => 'section added successfully', 'data' => $section]);

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
        $section = Section::findOrFail($id);
        $section->name = $request->name;
        $section->save();

        return response()->json(['success' => true, 'message' => 'Section updated successfully', 'data' =>$section]);
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
        $section = Section::findOrFail($id);
        $section->delete();

        return response()->json(['success' => true, 'message' => 'Section deleted successfully']);
    }
}
