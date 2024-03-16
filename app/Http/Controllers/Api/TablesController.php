<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Floor;
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
        return response()->json(["success" => true, "data" => $table]);
    }

    public function setSections(Request $request)
    {
        $user = Auth::user();
        $restaurant_id = $user->restaurant_id;
        $table = new Tables();
        $table->floor_id  = $request->floor_id ;
        $table->restaurant_id = $user->restaurant_id;
        $table->floor_number  = $request->floor_number;
        $table->tables = $request->tables;
        $table->save();
        return response()->json(['success' => true, 'message' => 'table added successfully', 'data' => $table]);
    }


    public function getFloorsAndTables(Request $request)
    {
        $user = Auth::user();
        $restaurant_id = $user->restaurant_id;
        $floors = Floor::where('restaurant_id', $restaurant_id)->get()->map->only(['floor'])->values();
        // $floors = $floors->pluck('floor')->g;
        $tables = Tables::where('restaurant_id', $restaurant_id)->get()->map->only(['floor_number', 'tables'])->values();
        return response()->json(['success' => true, 'message' => 'Floors and Tables', 'floors' => $floors, 'tables' => $tables]);
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
