<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FloorResource;
use App\Models\Floor;
use App\Models\KOT;
use App\Models\Section;
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
        $floors = Floor::where('restaurant_id', $user->restaurant_id)->with(['sections'])->get();

        if(!$floors)
        {
            return response()->json(["success"=>false, "message"=>"Data does exists for this Restaurant"]);
        }
        $floors = FloorResource::collection($floors);
        return response()->json(['success' => true, 'data' => $floors ]);
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

        $existing_floor = Floor::where(['restaurant_id'=> $user->restaurant_id, 'floor_name'=>$request->floor_name])->first();

        if ($existing_floor) {
            return response()->json(['success' => true, 'message' => 'Floor Data already exists for this Restaurant Id.']);
        }

        $floor = new Floor();
        $floor->restaurant_id = $user->restaurant_id;
        $floor->floor_name = $request->floor_name;
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
        $floor = new FloorResource($floor);
        return response()->json(['success' => true, 'message' => 'Floor data', 'data' => $floor]);
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

        $floor = Floor::findOrFail($id);
        $floor->floor_name = $request->floor_name;
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
        $floor->sections()->detach(); // Pivot Data in Floor Section of this Floor will also be deleted. 
        $name = $floor->floor_name;
        $floor->delete();

        return response()->json(['success' => true, 'message' => $name . ' is Deleted Successfully']);
    }

    public function setSectionAndTables(Request $request)
    {
        $request->validate([
            'floor_id',
            'section_id',
            'tables_count'
        ]);
        
        $user = Auth::user();
        $floor_id = $request->floor_id;
        $section_ids = $request->section_ids;
        $tables_counts = $request->tables_counts;

        $floor = Floor::findOrFail($floor_id);

        if (count($section_ids) !== count($tables_counts)) {
            return response()->json(['error' => 'Mismatch between section_ids and tables_counts.'], 400);
        }

        // Detach sections not included in the request
        $existingSections = $floor->sections()->pluck('section_id')->toArray();
        $sectionsToDetach = array_diff($existingSections, $section_ids);
        $floor->sections()->detach($sectionsToDetach);

        foreach ($section_ids as $index => $section_id) {
            $tables_count = $tables_counts[$index];
            $section = Section::findOrFail($section_id);
            $floor->sections()->syncWithoutDetaching([$section_id => ['tables_count' => $tables_count]]);
        }

        // $floor->sections()->syncWithPivotValues($section_ids,["tables_count"=>$tables_counts]);

        return response()->json(['success' => true, 'message' =>'Section and Tables_Count updated Successfully to ' . $floor->floor_name]);
    }

    function table_transfer(Request $request)
    {
        $user = Auth::user();
       
        $table_id = $request->table_id;
        
        $floor_id = $request->floor_id;
        $section_id = $request->section_id;
        $table_number = $request->table_number;


        $kot = KOT::where(['restaurant_id' => $user->restaurant_id,'status' => 'PENDING','table_id'=>$table_id])
                           ->first();


        if($kot)
        {
            $kot->table_number = $table_number; 
            $kot->section_id = $section_id; 
            $kot->floor_number = $floor_id;            
            
            $kot->save();


        }

        return response()->json(['success' => true, 'message' =>'Table Transfer successfully!!!']);

       

        




    }
}
