<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventorySupplierController extends Controller
{
    public function inventoryList()
    {
        $user = Auth::user();

        $data = Supplier::where('restaurant_id',$user->restaurant_id)->get();

        return response()->json(["success"=>true,$data]);

    }

    public function createSupplier(Request $request)
    {
        $user = Auth::user();

        $validator = $request->validate([
            'mobile' => 'required|numeric',
            'name' => 'required|string',
            'gstin' => 'required|string',  
            'c_person' => 'nullable|string',
            'c_number' => 'nullable|numeric'
        ]);

        $data = new Supplier;
        $data->restaurant_id = $user->restaurant_id;        
        $data->mobile = $request->mobile;
        $data->name = $request->name;
        $data->gstin = $request->gstin;
        $data->c_person=$request->c_person;
        $data->c_number=$request->c_number;
        $data->save();

        return response()->json(['success'=>true,'message'=>"Supplier added Successfully","data"=>$data]);

    }

    public function updateSupplier(Request $request, $id)
    {
        $user = Auth::user();

        $validator = $request->validate([
            'mobile' => 'required|numeric',
            'name' => 'required|string',
            'gstin' => 'required|string',
            'c_person' => 'nullable|string',
            'c_number' => 'nullable|numeric'
        ]);

        $data = Supplier::where('restaurant_id', $user->restaurant_id)->findOrFail($id);

        $data->mobile = $request->mobile;
        $data->name = $request->name;
        $data->gstin = $request->gstin;
        $data->c_person = $request->c_person;
        $data->c_number = $request->c_number;
        $data->save();

        return response()->json(['success' => true, 'message' => "Supplier updated Successfully", "data" => $data]);
    }

    public function deleteSupplier($id)
    {
        $user = Auth::user();

        $supplier = Supplier::where('restaurant_id', $user->restaurant_id)->findOrFail($id);

        $supplier->delete();

        return response()->json(['success' => true, 'message' => "Supplier deleted successfully"]);
    }
}
