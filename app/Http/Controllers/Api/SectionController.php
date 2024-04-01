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
        $user = Auth::user();
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
         $user = Auth::user();

        $existing_section = Section::where(['restaurant_id' => $user->restaurant_id, 'name' => $request->name])->first();

        if ($existing_section) {
            return response()->json(['success' => true, 'message' => 'Section Data already exists for this Restaurant Id.']);
        }
        // $data1 = User::where('id', $user->id)->get()->toArray(); // Corrected $user->Id to $user->id
        $section = new Section();
        $section->user_id = $user->id;
        $section->name = $request->name;
        $section->restaurant_id  = $user->restaurant_id;
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
        $user = Auth::user();
        $section = Section::findOrFail($id);
        return response()->json(["success" => true, "data" => $section]);
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
        $user = Auth::user();
        $section = Section::findOrFail($id);
        $section->floors()->detach(); // Also Removing the sections data in Floor_Section Table
        $section->delete();

        return response()->json(['success' => true, 'message' => 'Section deleted successfully']);
    }
}
