<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TableActiveResource;
use App\Http\Resources\TableResource;
use App\Http\Resources\TableSectionResource;
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

    public function store(Request $request)
    {
        $user = Auth::user();

        $Tables = new Tables();
        $Tables->restaurant_id  = $user->restaurant_id;
        $Tables->floor_id = $request->floor_id;
        $Tables->section_id = $request->section_id;
        $Tables->floor_number = $request->floor_number;
        $Tables->tables = $request->tables;
        $Tables->save();

        return response()->json(['success' => true, 'message' => 'Tables added successfully', 'data' => $Tables]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $Tables = Tables::findOrFail($id);
        return response()->json(["success" => true, "data" => $Tables]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $Tables = Tables::findOrFail($id);
        $Tables->restaurant_id  = $user->restaurant_id;
        $Tables->floor_id = $request->floor_id;
        $Tables->section_id = $request->section_id;
        $Tables->floor_number = $request->floor_number;
        $Tables->tables = $request->tables;

        $Tables->save();

        return response()->json(['success' => true, 'message' => 'Tables updated successfully', 'data' => $Tables]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $Tables = Tables::findOrFail($id);
        $Tables->delete();

        return response()->json(['success' => true, 'message' => 'Tables deleted successfully']);
    }

    public function getSections(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'floor_number' => 'required',
        ]);
        $floor = FloorSection::where(['restaurant_id' => $user->restaurant_id, 'floor' => $request->floor_number])->first();
        if($floor){
            $sections_count = $floor->sections_count;
            $sections = Tables::where(['restaurant_id' => $user->restaurant_id, 'floor_number' => $request->floor_number])->get(); //->count();
            $sections = TableSectionResource::collection($sections);// $sections->map->only(['floor_number','section_id','tables']);
            return response()->json(['success' => true, 'message' => 'Sections data of Floor '.$request->floor_number, 'sections_count' => $sections_count, 'data' => $sections]);
        }
        else{
            return response()->json(["success"=>false,"message"=>"Floor Doesn't exists"]);
        }

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
        $sections = Tables::where(['restaurant_id' => $user->restaurant_id, 'floor_number' => $request->floor_number])->get();//->count();
        $sections_count = count($sections);

        if($sections_count < $main_sections_count)
        {
            $table = new Tables();
            $table->floor_id  = $request->floor_id ?? null;
            $table->restaurant_id = $user->restaurant_id;
            $table->floor_number  = $request->floor_number;
            $table->section_id = $request->section_id;
            $table->save();
            return response()->json(['success' => true, 'message' => 'Section_Id added successfully to Table', 'data' => $table]);
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
        
        $tables = Tables::where('restaurant_id', $restaurant_id)->get();//->map->only(['floor_number','section_id','tables'])->values();

        $tables = TableResource::collection($tables); 

        // $activeTables = TableActive::whereIn('id', function ($query) use ($user) {
        //     $query->select(DB::raw('MIN(id)'))
        //     ->from('table_actives')
        //     ->where('restaurant_id', $user->restaurant_id)
        //         ->groupBy('table_number');
        // })->get();

        // $activeTables = TableActiveResource::collection($activeTables);
        
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

        $table = Tables::where(['restaurant_id'=>$user->restaurant_id, 'floor_number' => $request->floor_number, 'section_id' => $request->section_id])->first();
        
        if(!$table)
        {
            return response()->json(["success"=>false, "message"=>"Table Data does not exists for this section_id and floor_number. "]);
        }

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
            'old_section_id' => 'required',
            'new_section_id' => 'required',
        ]);

        $table = Tables::where(['restaurant_id' => $user->restaurant_id, 'floor_number' => $request->floor_number, "section_id"=> $request->old_section_id])->first();

        if(!$table){
            return response()->json(["success"=>false,"message"=>"Table data doesn't exists for this floor_number and section_id"]);
        }
        
        $table->section_id = $request->new_section_id;
        $table->save();

        return response()->json(["success" => true, "message" => "SectionId Updated Successfully for Given Floor"]);
    }

    public function deleteSection(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'floor_id' => '',
            'floor_number' => 'required',
            'section_id' => 'required',
        ]);

        $table = Tables::where(['restaurant_id' => $user->restaurant_id, 'floor_number' => $request->floor_number, "section_id"=> $request->section_id])->first();

        if(!$table) {
            return response()->json(["success" => false, "message" => "Table data doesn't exists for the given floor_number and section_id"]);
        }
        
        $table->delete();
           
        // $floorSection = FloorSection::where(['restaurant_id' => $user->restaurant_id, 'floor' => $request->floor_number])->first(); 
        // $sections_count = $floorSection->sections_count;
        // $sections_count = $sections_count - 1;
        // $floorSection->save(); 

        return response()->json(["success" => true, "message" => "Section Deleted Successfully"]);
    }

    // public function setTables(Request $request)
    // {
    //     $user = Auth::user();

    //     $request->validate([
    //         'floor_id' => '',
    //         'floor_number' => 'required',
    //         'section_id' => 'required',
    //         'tables' =>'required',
    //     ]);

    //     $table = Tables::where(['restaurant_id'=>$user->restaurant_id, 'floor_number' => $request->floor, 'section_id' => $request->section_id])->get();
    //     $table->tables = $request->tables;
    //     $table->save();

    //     return response()->json(["success" => true, "message" => "Tables Count Added/Updated Successfully"]);
    // }

    // public function deleteSection(Request $request)
    // {
    //     $user = Auth::user();

    //     $request->validate([
    //         'floor_id' => '',
    //         'floor_number' => 'required',
    //         'section_id' => 'required',
    //     ]);

    //     $section = Tables::where(['restaurant_id' => $user->restaurant, 'floor' => $request->floor_number, "section_id", $request->section_id])->first();
    //     $section->delete();
           
    //     $floorSection = FloorSection::where(['restaurant_id' => $user->restaurant_id, 'floor' => $request->floor_number])->first(); 
    //     $sections_count = $floorSection->sections_count;
    //     $sections_count = $sections_count - 1;
    //     $floorSection->save(); 

    //     return response()->json(["success" => true, "message" => "Section Deleted Successfully"]);
    // }
}
