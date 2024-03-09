<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //  return $request->all();
        //
        $user = Auth::user();
        
        $resturants_id  = $request->input('resturant_id');
       


        // $location = Location::find($locationId);
        
        $data1 = User::where('id', $user->id)->get()->toArray(); // Corrected $user->Id to $user->id
        
        $categories = Category::where('restaurant_id',$user->restaurant_id)
                    
                    ->get();
        
        return response()->json(["success" => true, "data" => $categories]);
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $user = Auth::user();
        $restaurant_id = $user->restaurant_id;
        $category = new Category();
        // $category->category_id  = $request->category_id ;
        $category->category_name = $request->category_name;
        $category->restaurant_id  = $request->restaurant_id ;
        $category->descirption = $request->descirption;
        $category->restaurant_id = $restaurant_id;
        $category->save();
        return response()->json(['success' => true, 'message' => 'Category added successfully', 'data' => $category]);

        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

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
        //
        $validatedData = $request->validate([
            'category_id' => 'required|string|max:255',
            'category_name' => 'required|string|max:255',
            'descirption' => 'required|string|max:255',
            



        ]);
        $user = Auth::user();
        $category = Category::find($id);
        $category->category_id  = $request->category_id ;
        $category->category_name = $request->category_name;
        $category->restaurant_id  = $request->restaurant_id ;
        $category->descirption = $request->descirption;
        // $category->restaurant_id = $restaurant_id;
        $category->save();
        // $category->update($validatedData);
        return response()->json(['success' => true, 'message' => 'Category updated successfully','data'=>$category]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $category = Category::findorFail($id);
        $name = $category->category_name;
        $category->delete();
        return response()->json(["success" => true, "message" => $name . ' category is Deleted Successfully']);
    }
}
