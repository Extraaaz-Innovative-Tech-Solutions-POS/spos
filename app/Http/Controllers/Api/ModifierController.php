<?php

namespace App\Http\Controllers\Api;

use App\Exports\ModifierExport;
use App\Http\Controllers\Controller;
use App\Imports\ModifierImport;
use App\Models\Modifier;
use App\Models\ModifierGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ModifierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $modifier = Modifier::where('restaurant_id', $user->restaurant_id)->latest()->get();

        return response()->json(['success' => true, 'data' => $modifier]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            // "description" => "Its Drinks Groups",
            "price" => "required"
        ]);
        $user = Auth::user();

        $modifier = new Modifier();
        $modifier->user_id = $user->id;
        $modifier->name = $request->name;
        $modifier->short_name = $request->short_name ?? null;
        $modifier->description = $request->description ?? null;
        $modifier->price = $request->price;
        $modifier->restaurant_id = $user->restaurant_id;
        $modifier->save();

        return response()->json(["success" => true, "message" => "Data saved successfully", "modifier" => $modifier]);
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
        $modifier = Modifier::findOrFail($id);
        return response()->json(["success" => true, "data" => $modifier]);
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
        $modifier = Modifier::findOrFail($id);
        $modifier->name = $request->name;
        $modifier->short_name = $request->short_name ?? null;
        $modifier->description = $request->description ?? null;
        $modifier->price = $request->price;
        $modifier->save();

        return response()->json(["success" => true, "message" => "Data Updated successfully", "data" => $modifier]);
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

        $modifier = Modifier::findOrFail($id);
        $modifierName = $modifier->name;
        $modifier->delete();

        return response()->json(["success" => true, "message" => $modifierName . " deleted successfully",]);
    }

    public function showModifierGroups(Request $request, $modifier_id)
    {
        $user = Auth::user();

        $modifier = Modifier::findOrFail($modifier_id);
        $modifierGroups = $modifier->modifierGroups;

        if (!$modifierGroups) {
            return response()->json(["success" => false, "message" => "There are no ModifierGroups for this Modifier"]);
        }

        return response()->json(['success' => true, 'message' => 'Modifier Groups of Modifier ' . $modifier->name, 'modifierGroups' => $modifierGroups]);
    }

    public function selectModifierGroups(Request $request, $modifier_id)
    {
        $user = Auth::user();

        $ids = [];

        $modifierGroupsAll = ModifierGroup::where('restaurant_id', $user->restaurant_id)->get();
        $modifier = Modifier::findOrFail($modifier_id);

        foreach ($modifier->modifierGroups as $modifierGroup) {
            array_push($ids, $modifierGroup->id);
        }

        return response()->json(['success' => true, 'message' => 'Ids of ModifierGroups of associated with this Modifier', 'ids' => $ids, 'modifierGroupsAll' => $modifierGroupsAll]);
    }

    public function saveModifierGroups(Request $request, $modifier_id)
    {
        $user = Auth::user();

        $modifier = Modifier::findOrFail($modifier_id);

        $selectedIds = $request->ids;

        $modifier->modifierGroups()->sync($selectedIds);

        return response()->json(["success" => true, 'message' => 'ModifierGroups and Modifier synced Successfully']);
    }

    public function importModifiers(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate(['file' => 'required']);

        if ($request->hasFile('file')) {
            Excel::import(new ModifierImport, $request->file('file'));
        }

        return response()->json(['success' => 'File uploaded successfully']);
    }

    public function exportModifiers(Request $request)
    {
        return Excel::download(new ModifierExport, 'modifierExport.xlsx');
    }
}
