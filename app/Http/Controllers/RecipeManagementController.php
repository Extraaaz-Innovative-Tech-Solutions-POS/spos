<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipeManagementController extends Controller
{
    //

    public function storeRecipe(Request $request)
    {
        // Get the authenticated user and their restaurant ID
        $user = Auth::user();
        $restaurant_id = $user->restaurant_id;
    
        // Get the necessary request inputs
        $recipe_id = $request->input('recipe_id');
        $ingredientIDs = $request->input('ingredientID');
    
        // Ensure ingredientIDs is an array
        if (!is_array($ingredientIDs)) {
            return response()->json(['error' => 'ingredientID must be an array'], 400);
        }
    
        try {
            foreach ($ingredientIDs as $ingredientID) {
                $existingItem = Ingredient::where('id', $ingredientID)
                    ->where('restaurant_id', $restaurant_id)
                    ->first();
    
                if ($existingItem) {
                    $existingRecipeIds = $existingItem->recipe_id ?: [];
    
                    if (!in_array($recipe_id, $existingRecipeIds)) {
                        $existingRecipeIds[] = $recipe_id;
                        $existingItem->recipe_id = $existingRecipeIds;
                        $existingItem->save();
                    }
                }
            }
    
            $existingRecipe = Recipe::where('recipe_id', $recipe_id)
                ->where('restaurant_id', $restaurant_id)
                ->first();
    
            if ($existingRecipe) {
                $existingIngredients = $existingRecipe->ingredients ?: [];
                $newIngredients = $request->input('products') ?: [];
                $updatedIngredients = array_merge($existingIngredients, $newIngredients);
                $existingRecipe->ingredients = $updatedIngredients;
                $existingRecipe->save();
            } else {
                $recipe = new Recipe();
                $recipe->recipe_name = $request->input('recipeName');
                $recipe->recipe_id = $recipe_id;
                $recipe->recipe_pos_id = $request->input('recipePosId');
                $recipe->ingredients = $request->input('products') ?: [];
                $recipe->restaurant_id = $restaurant_id;
                $recipe->save();
            }
    
            return response()->json(['message' => 'Recipe stored successfully'], 200);
        } catch (\Exception $e) {
            // Return an error response if storing the recipe fails
            return response()->json(['error' => 'Failed to store recipe', 'message' => $e->getMessage()], 500);
        }
    }



    public function deleteRecipe(Request $request)
{
    // Get the authenticated user and their restaurant ID
    $user = Auth::user();
    $restaurant_id = $user->restaurant_id;

    // Get the necessary request input
    $recipe_id = $request->input('recipe_id');

    try {
        // Find the recipe by recipe_id and restaurant_id
        $recipe = Recipe::where('recipe_id', $recipe_id)
                        ->where('restaurant_id', $restaurant_id)
                        ->first();

        if (!$recipe) {
            // Return an error response if the recipe is not found
            return response()->json(['error' => 'Recipe not found'], 404);
        }

        // Delete the recipe
        $recipe->delete();

        return response()->json(['message' => 'Recipe deleted successfully'], 200);
    } catch (\Exception $e) {
        // Return an error response if deleting the recipe fails
        return response()->json(['error' => 'Failed to delete recipe', 'message' => $e->getMessage()], 500);
    }
}



public function getAllRecipes()
{
    // Get the authenticated user and their restaurant ID
    $user = Auth::user();
    $restaurant_id = $user->restaurant_id;

    try {
        // Retrieve all recipes for the given restaurant
        $recipes = Recipe::where('restaurant_id', $restaurant_id)->get();

        return response()->json([
            'message' => 'Recipes retrieved successfully',
            'recipes' => $recipes
        ], 200);
    } catch (\Exception $e) {
        // Return an error response if fetching the recipes fails
        return response()->json(['error' => 'Failed to retrieve recipes', 'message' => $e->getMessage()], 500);
    }
}


}
