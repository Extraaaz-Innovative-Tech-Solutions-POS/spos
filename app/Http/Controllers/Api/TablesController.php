<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Floor;
use App\Models\FloorSection;
use App\Models\Tables;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TablesController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();

        $table = Tables::where('restaurant_id',$user->restaurant_id)->get();
        if(!$table)
        {
            return response()->json(["success"=> false, "message"=>"Table Data doesn't exists for this restaurant."]);
        }
        return response()->json(["success" => true, "data" => $table]);
    }

    public function setSection(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'floor_id' => '',
            'floor_number' => 'required',
            'section_id' => 'required',
        ]);

        $main_sections_count = FloorSection::where(['restaurant_id' => $user->restaurant_id, 'floor' => $request->floor_number])->first()->sections_count;
        $sections_count =  Tables::where(['restaurant_id' => $user->restaurant, 'floor' => $request->floor_number])->get();

        if($sections_count <= $main_sections_count)
        {
            $table = new Tables();
            $table->floor_id  = $request->floor_id ?? null;
            $table->restaurant_id = $user->restaurant_id;
            $table->floor_number  = $request->floor_number;
            $table->tables = $request->section_id;
            $table->save();
            return response()->json(['success' => true, 'message' => 'table added successfully', 'data' => $table]);
        }
        else{
            return response()->json(['success'=> false, 'message'=>'Cannot add sections more than the alloted no of sections. Please delete sections or increase the number of sections.']);
        }
    }


    public function getFloorsAndTables(Request $request)
    {
        $user = Auth::user();
        
        $restaurant_id = $user->restaurant_id;
        
        $floors = Floor::where('restaurant_id', $restaurant_id)->get()->map->only(['floor'])->first();
        
        $sections = FloorSection::where('restaurant_id', $restaurant_id)->get()->map->only(['floor','sections_count'])->values();
        
        $tables = Tables::where('restaurant_id', $restaurant_id)->get()->map->only(['floor_number','section_id','tables'])->values();
        
        return response()->json(['success' => true, 'message' => 'Floors and Tables', 'floors' => $floors, 'sections' => $sections, 'tables' => $tables]);
    }

    public function setTables(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'floor_id' => '',
            'floor_number' => 'required',
            'section_id' => 'required',
            'tables' =>'required',
        ]);

        $table = Tables::where(['restaurant_id'=>$user->restaurant_id, 'floor_number' => $request->floor, 'section_id' => $request->section_id])->get();
        $table->tables = $request->tables;
        $table->save();

        return response()->json(["success" => true, "message" => "Tables Count Added/Updated Successfully"]);
    }

    public function updateSection(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'floor_id' => '',
            'floor_number' => 'required',
            'section_id' => 'required',
        ]);

        $section = Tables::where(['restaurant_id' => $user->restaurant, 'floor' => $request->floor_number, "section_id", $request->section_id])->first();
        $section->section_id = $request->section_id;
        $section->save();

        return response()->json(["success" => true, "message" => "Section Deleted Successfully"]);
    }

    public function deleteSection(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'floor_id' => '',
            'floor_number' => 'required',
            'section_id' => 'required',
        ]);

        $section = Tables::where(['restaurant_id' => $user->restaurant, 'floor' => $request->floor_number, "section_id", $request->section_id])->first();
        $section->delete();
           
        $floorSection = FloorSection::where(['restaurant_id' => $user->restaurant_id, 'floor' => $request->floor_number])->first(); 
        $sections_count = $floorSection->sections_count;
        $sections_count = $sections_count - 1;
        $floorSection->save(); 

        return response()->json(["success" => true, "message" => "Section Deleted Successfully"]);
    }

    public function setTables(Request $request)
    {
        $user = Auth::user();

        $table = Tables::where("restaurant_id");
        $table->floor_id  = $request->floor_id;
        $table->restaurant_id = $user->restaurant_id;
        $table->floor_number  = $request->floor_number;
        $table->tables = $request->tables;
        $table->save();
        return response()->json();
    }

    public function updateTables(Request $request)
    {
        $user = Auth::user();

    }
}
