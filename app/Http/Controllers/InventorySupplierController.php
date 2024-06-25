<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventorySupplierController extends Controller
{
    public function inventoryList()
    {
        $user = Auth::user();

        $data = Supplier::where('restaurant_id',$user->restaurant_id)->get();

        return response()->json(["success"=>true,"data"=>$data]);

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

        // Check if the supplier already exists
        $existingSupplier = Supplier::where('mobile', $request->mobile)->where('restaurant_id', $user->restaurant_id)->first();

        if ($existingSupplier) {
            return response()->json(['success' => false, 'message' => 'Supplier already exists'], 409);
        }

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
    
        try {
            $data = Supplier::where('restaurant_id', $user->restaurant_id)->findOrFail($id);
    
            $data->mobile = $request->mobile;
            $data->name = $request->name;
            $data->gstin = $request->gstin;
            $data->c_person = $request->c_person;
            $data->c_number = $request->c_number;
            $data->save();
    
            return response()->json(['success' => true, 'message' => "Supplier updated Successfully", "data" => $data]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => "Supplier not found"], 404);
        }
    }
    

    public function deleteSupplier($id)
    {
        $user = Auth::user();
    
        try {
            $supplier = Supplier::where('restaurant_id', $user->restaurant_id)->findOrFail($id);
            $supplier->delete();
    
            return response()->json(['success' => true, 'message' => "Supplier deleted successfully"]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => "Supplier not found"], 404);
        }
    }


  


    public function searchSupplier(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Validate the request input
        $validator = $request->validate([
            'query' => 'required|string|max:255',
        ]);

        // Get the search query
        $query = $request->input('query');

        // Search suppliers by name, mobile, or other fields
        $suppliers = Supplier::where('restaurant_id', $user->restaurant_id)
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('mobile', 'LIKE', "%{$query}%")
                  ->orWhere('gstin', 'LIKE', "%{$query}%")
                  ->orWhere('c_person', 'LIKE', "%{$query}%")
                  ->orWhere('c_number', 'LIKE', "%{$query}%");
            })
            ->get();

        // Return the search results
        return response()->json(['success' => true, 'message' => 'Search results', 'data' => $suppliers]);
    }
    
}
