<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Modifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $modifier = Modifier::where('restaurant_id', $user->restaurant_id)->latest();

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
        $user = Auth::user();

        $modifier = new Modifier();

        $modifier->user_id = $user->id;
        $modifier->name = $request->name;
        $modifier->short_name = $request->short_name ?? null;
        $modifier->description = $request->description ?? null;
        $modifier->price = $request->price;
        // $modifier->restaurant_id = $user->restaurant_id;
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
        return response()->json(["success" => true, "message" => "Data saved successfully", "modifier" => $modifier]);
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

        return response()->json(["success" => true, "message" => "Data saved successfully", "modifier" => $modifier]);
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
}
