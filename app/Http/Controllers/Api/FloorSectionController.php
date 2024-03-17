<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FloorSection;
use App\Models\Tables;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FloorSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $floorSection = FloorSection::where('restaurant_id', $user->restaurant_id)->get();

        return response()->json(["success" => true, "data" => $floorSection]);
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

        $request->validate([
            'floor' => 'required',
            'sections_count' => 'required|numeric|max:3',
        ]);

        $existingSection = FloorSection::where('floor', $request->floor)
            // ->where('section', $request->section)
            ->where('restaurant_id', $restaurant_id)
            ->first();

        if ($existingSection) {
            return response()->json(['success' => false, 'message' => 'An entry of sections for this table already exists for this restaurant.']);
        }

        $section = new FloorSection();
        $section->user_id = $user->id;
        $section->floor = $request->floor;
        $section->sections_count = $request->sections_count;
        $section->restaurant_id = $restaurant_id;
        $section->save();

        return response()->json(['success' => true, 'message' => 'Section floor added successfully', 'data' => $section]); 
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
        $user = Auth::user();

        $section = FloorSection::findOrFail($id);
        $section->floor = $request->floor;
        $section->sections_count = $request->sections_count;
        $section->save();
        
        return response()->json(['success' => true, 'message' => 'Section updated successfully', 'data' => $section]);
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
        $section = FloorSection::findOrFail($id);
        $section->delete();
        return response()->json(['success' => true, 'message' => 'Section deleted successfully']);
    }
}
