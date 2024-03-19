<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Items;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Database\Eloquent\Relations\HasMany;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $categories = Category::where('restaurant_id',$user->restaurant_id)->get();
        $categories = CategoryResource::collection($categories);

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
        $category->description = $request->description;
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
        $validatedData = $request->validate([
            'category_id' => 'required',
            'category_name' => 'required',
            'description' => 'required',
        ]);

        $user = Auth::user();
        $category = Category::find($id);
        $category->category_id  = $request->category_id ;
        $category->category_name = $request->category_name;
        $category->description = $request->description;
        $category->save();
        
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
        $category = Category::findOrFail($id);
        $name = $category->category_name;
        $category->delete();
        return response()->json(["success" => true, "message" => $name . ' category is Deleted Successfully']);
    }
}
