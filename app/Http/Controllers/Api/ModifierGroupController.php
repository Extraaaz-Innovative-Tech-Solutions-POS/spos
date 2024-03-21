<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Modifier;
use App\Models\ModifierGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModifierGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $modifierGroup = ModifierGroup::where('restaurant_id', $user->restaurant_id)->latest()->get();
        return response()->json(['success' => true, 'data' => $modifierGroup]);
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
            "type" => "required"
        ]);
        
        $user = Auth::user();

        $modifierGroup = new ModifierGroup();

        $modifierGroup->user_id = $user->id;
        $modifierGroup->name = $request->name;
        $modifierGroup->description = $request->description;
        $modifierGroup->type = $request->type;
        $modifierGroup->restaurant_id = $user->restaurant_id;
        $modifierGroup->save();

        return response()->json(["success"=>true,"message" => "Data saved successfully", "data"=>$modifierGroup]);
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
        $modifierGroup = ModifierGroup::findOrFail($id);
        return response()->json(["success" => true, "data" => $modifierGroup]);
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
        $modifierGroup = ModifierGroup::findOrFail($id);
        $modifierGroup->name = $request->name;
        $modifierGroup->description = $request->description;
        $modifierGroup->type = $request->type;
        $modifierGroup->save();

        return response()->json(["success" => true, "message" => "Data Updated successfully", "data" => $modifierGroup]);       
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

        $modifierGroup = ModifierGroup::findOrFail($id);
        $modifierGroupName = $modifierGroup->name;
        $modifierGroup->delete();

        return response()->json(["success" => true, "message" => $modifierGroupName ." deleted successfully", ]);
    }

    public function showModifiers(Request $request, $modifierGroup_id)
    {
        $user = Auth::user();

        $modifierGroup = ModifierGroup::findOrFail($modifierGroup_id);
        $modifiers = $modifierGroup->modifiers;

        if(!$modifiers){
            return response()->json(["success" => false, "message" =>"There are no modifiers for this Modifier group"]);
        }

        return response()->json(['success' => true, 'message' => 'Modifiers of this modifierGroup ' . $modifierGroup->name, 'data' => $modifiers]);
    }

    public function selectModifiers(Request $request, $modifierGroup_id)
    {
        $user = Auth::user();

        $ids = [];

        $modifiersAll = Modifier::where('restaurant_id', $user->restaurant_id)->get();
        $modifierGroup = ModifierGroup::findOrFail($modifierGroup_id);

        foreach ($modifierGroup->modifiers as $modifier) {
            array_push($ids, $modifier->id);
        }

        return response()->json(['success' => true, 'message' => 'Ids of Modifiers of associated with this ModifierGroup', 'ids' => $ids, 'modifiersAll' => $modifiersAll]);
    }

    public function saveModifiers(Request $request, $modifierGroup_id)
    {
        $user = Auth::user();

        $modifierGroup = ModifierGroup::findOrFail($modifierGroup_id);

        $selectedIds = $request->ids;

        $modifierGroup->modifiers()->sync($selectedIds);

        return response()->json(["success" => true, 'message' => 'ModifierGroups and Modifiers synced Successfully']);
    }

    public function showItems(Request $request, $modifierGroup_id)
    {
        $user = Auth::user();

        $modifierGroup = ModifierGroup::findOrFail($modifierGroup_id);
        $items = $modifierGroup->items;

        if (!$items) {
            return response()->json(["success" => false, "message" => "There are no items for this Modifier group"]);
        }

        return response()->json(['success' => true, 'message' => 'Items of ModifierGroups ' . $modifierGroup->name, 'data' => $items]);
    }

    public function selectItems(Request $request, $modifierGroup_id)
    {
        $user = Auth::user();

        $ids = [];

        $itemsAll = Item::where('restaurant_id', $user->restaurant_id)->get();
        $modifierGroup = ModifierGroup::findOrFail($modifierGroup_id);

        foreach ($modifierGroup->items as $item) {
            array_push($ids, $item->id);
        }

        return response()->json(['success' => true, 'message' => 'Ids of Items of associated with this ModifierGroup', 'ids' => $ids, 'itemsAll' => $itemsAll]);
    }

    public function saveItems(Request $request, $modifierGroup_id)
    {
        $user = Auth::user();

        $modifierGroup = ModifierGroup::findOrFail($modifierGroup_id);

        $selectedIds = $request->ids;

        $modifierGroup->items()->sync($selectedIds);

        return response()->json(["success" => true, 'message' => 'ModifierGroups and Items synced Successfully']);
    }
}
