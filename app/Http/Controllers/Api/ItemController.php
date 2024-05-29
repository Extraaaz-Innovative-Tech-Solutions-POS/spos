<?php

namespace App\Http\Controllers\Api;

use App\Exports\CategoryExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemPricingResource;
use App\Http\Resources\ItemResource;
use App\Http\Resources\ItemSectionPriceCollection;
use App\Http\Resources\ItemSectionPriceResource;
use App\Http\Resources\ModifierGroupResource;
use App\Imports\CategoryImport;
use App\Imports\ItemImport;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemPricing;
use App\Models\ModifierGroup;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $Item = Item::where('restaurant_id', $user->restaurant_id)->with(['modifierGroups', 'sectionWisePricings'])->get();

        $Item = ItemResource::collection($Item);

        return response()->json(['success' => true, 'data' => $Item]);
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
        // $data1 = User::where('id', $user->id)->get()->toArray(); // Corrected $user->Id to $user->id
        $restaurant_id = $user->restaurant_id;

        $lastShortCode = Item::where('restaurant_id', $restaurant_id)
        ->orderBy('short_code', 'desc')
        ->value('short_code');

        //Increment the last short_code and assign it to the new item
        $nextShortCode = $lastShortCode ? $lastShortCode + 1 : 1;

        $Item = new Item();
        // $Item->item_id  = $request->item_id;
        $Item->item_name = $request->item_name;
        $Item->restaurant_id = $restaurant_id;
        $Item->price = $request->price ? $request->price : null;
        $Item->discount = $request->discount ? $request->discount : null;
        $Item->category_id = $request->category_id ? $request->category_id : null;
        $Item->food_type = $request->food_type ? $request->food_type : null;
        $Item->inventory_status = $request->inventory_status ? $request->inventory_status : null;
        $Item->associated_item = $request->associated_item ? $request->associated_item : null;
        $Item->varients = $request->varients ? $request->varients : null;
        $Item->tax_percentage = $request->tax_percentage ? $request->tax_percentage : null;
        $Item->short_code = $nextShortCode; 

        $Item->save();

        return response()->json(['success' => true, 'message' => 'Item saved successfully', 'data' => $Item]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Item::with(['modifierGroups', 'sectionWisePricings'])->findOrFail($id);
        $item = new ItemResource($item);

        return response()->json(['success' => true, 'message' =>'Item Data of ' . $item->item_name, 'data' => $item]);
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
            // 'item_id' => 'required|string|max:255',
            'item_name' => 'required|string|max:255',
            // 'price' => 'required|string|max:255',
            // 'discount' => 'required|string|max:255',
            // 'category_id' => 'required|string|max:255',
            // 'food_type' => 'string|max:255',
            // 'inventory_status' => 'string|max:255',
            // 'associated_item' => 'string|max:255',
            // 'varients' => 'string|max:255',
            // 'tax_percentage' => 'string|max:255',
            // 'varients' => 'string|max:255',
        ]);

        $user = Auth::user();

        $Item = Item::findOrFail($id);
        $Item->item_name = $request->item_name;
        $Item->restaurant_id = $user->restaurant_id;
        $Item->price = $request->price ? $request->price : null;
        $Item->discount = $request->discount ? $request->discount : null;
        $Item->category_id = $request->category_id ? $request->category_id : null;
        $Item->food_type = $request->food_type ? $request->food_type : null;
        $Item->inventory_status = $request->inventory_status ? $request->inventory_status : null;
        $Item->associated_item = $request->associated_item ? $request->associated_item : null;
        $Item->varients = $request->varients ? $request->varients : null;
        $Item->tax_percentage = $request->tax_percentage ? $request->tax_percentage : null;
        $Item->save();
        return response()->json(['success' => true, 'message' => 'Category updated successfully', 'data' => $Item]); //, 'data' => $category]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $name = $item->item_name;
        $item->delete();
        return response()->json(['success' => true, 'message' => $name . ' Item is Deleted Successfully']);
    }

    public function showModifierGroups(Request $request, $item_id)
    {
        $user = Auth::user();

        $item = Item::findOrFail($item_id);
        $modifierGroups = $item->modifierGroups;

        if (!$modifierGroups) {
            return response()->json(["success" => false, "message" => "There are no ModifierGroups for this Item"]);
        }

        $modifierGroups = ModifierGroupResource::collection($modifierGroups);

        return response()->json(['success' => true, 'message' => 'Modifier Groups of Item ' . $item->item_name, 'modifierGroups' => $modifierGroups]);
    }

    public function selectModifierGroups(Request $request, $item_id)
    {
        $user = Auth::user();

        $ids = [];

        $modifierGroupsAll = ModifierGroup::where('restaurant_id', $user->restaurant_id)->get();
        $item = Item::findOrFail($item_id);

        foreach ($item->modifierGroups as $modifierGroup) {
            array_push($ids, $modifierGroup->id);
        }

        return response()->json(['success' => true, 'message' => 'Ids of ModifierGroups of associated with this Item', 'ids' => $ids, 'modifierGroupsAll' => $modifierGroupsAll]);
    }

    public function saveModifierGroups(Request $request, $item_id)
    {
        $user = Auth::user();

        $item = Item::findOrFail($item_id);
        $selectedIds = $request->ids;
        $item->modifierGroups()->sync($selectedIds);

        return response()->json(["success" => true, 'message' => 'ModifierGroups and Items synced Successfully']);
    }

    public function getModifierGroups($item_id)
    {
        $item = Item::findOrFail($item_id);

        $modifierGroups = $item->modifierGroups;

        $modifierGroups = ModifierGroupResource::collection($modifierGroups);

        return response()->json(["success" => true, 'message' =>"Modifiers according to Item Id", 'data' => $modifierGroups]);
    }

    public function getSectionPrice(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'item_id' => 'required',
            'section_id' => 'required',
        ]);

        $item_id = $request->item_id;
        $section_id = $request->section_id;

        $itemPricing = ItemPricing::where(['restaurant_id' => $user->restaurant_id, 'item_id' => $item_id, 'section_id' => $section_id])->first();
        $itemPrice = new ItemPricingResource($itemPricing);
        $itemName = $itemPricing->item->item_name;

        return response()->json(['success' => true, 'message' => 'Data of item '. $itemName, 'data' =>  $itemPrice ]);
    }

    public function setSectionWisePrice(Request $request)
    {
        $request->validate([
            'item_id' => "required",
            'section_ids' => "required",
            'prices' => "required",
        ]);

        $user = Auth::user();
        $item_id = $request->item_id;
        $section_ids = $request->section_ids;
        $prices = $request->prices;

        $item = Item::findOrFail($item_id);

        if (count($section_ids) !== count($prices)) {
            return response()->json(['error' => 'Mismatch between section_ids and prices.'], 400);
        }

        // Detach sections not included in the request
        $existingSections = $item->sections()->pluck('section_id')->toArray();
        $sectionsToDetach = array_diff($existingSections, $section_ids);
        $item->sections()->detach($sectionsToDetach);

        foreach ($section_ids as $index => $section_id) {
            $price = $prices[$index];
            // $section = Section::findOrFail($section_id);
            $item->sections()->syncWithoutDetaching([$section_id => ['price' => $price]]);
        }

        return response()->json(['success' => true, 'message' => 'Section and Price updated Successfully to ' . $item->item_name]);
    }

    public function setSectionPrice(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'item_id' => 'required',
            'section_id' => 'required',
            'price' => 'required',
        ]);

        $item_id = $request->item_id;
        $section_id = $request->section_id;

        $itemPrice = ItemPricing::where(['restaurant_id' => $user->restaurant_id, 'item_id' => $item_id, 'section_id' => $section_id])->first();

        if($itemPrice)
        {
            $itemPrice->price = $request->price;
            $itemPrice->save();
        }
        else{
            $itemPrice = new ItemPricing();
            $itemPrice->item_id = $request->item_id;
            $itemPrice->section_id = $request->section_id;
            $itemPrice->price = $request->price;
            $itemPrice->user_id = $user->id;
            $itemPrice->restaurant_id = $user->restaurant_id;
            $itemPrice->save();
        }
        
        $itemPrice = new ItemPricingResource($itemPrice);

        return response()->json(['success'=>true,  'message'=> 'Item Price Added/Updated Successfully' , 'data' => $itemPrice]);
    }              

    public function updateSectionPrice(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'item_id' => 'required',
            'section_id' => 'required',
            'price' => 'required',
        ]);

        $item_id = $request->item_id;
        $section_id = $request->section_id;
        $price = $request->price;

        $itemPricing = ItemPricing::where(['restaurant_id' => $user->restaurant_id,'item_id'=>$item_id, 'section_id' => $section_id])->first();
        if (!$itemPricing) {
            return response()->json(['success' => false, 'message' => 'Item Price not found'], 404);
        }
        $itemPricing->price = $price;
        $itemPricing->save();

        $itemPricing = new ItemPricingResource($itemPricing);

        $itemName = $itemPricing->item->item_name;
        return response()->json(['success'=>true, 'message'=> $itemName . 'Item Price Updated  to '.$price .' Successfully']);
    }

    public function deleteSectionPrice(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'item_id' => 'required',
            'section_id' => 'required',
        ]);

        $item_id = $request->item_id;
        $section_id = $request->section_id;
        
        $itemPricing = ItemPricing::where(['restaurant_id' => $user->restaurant_id, 'item_id'=> $item_id, 'section_id' => $section_id])->first();
        
        if(!$itemPricing)
        {
            return response()->json(['success' => false, 'message' =>'Item Price not found'],404);
        }
        $itemName = $itemPricing->item->item_name;
        $itemPricing->delete();

        return response()->json(['success' => true, 'message' => $itemName . 'Item Price has been Deleted Successfully']);
    }


    //bulk upload for items

    public function bulkUploadItems(Request $request)
    {  
        $request->validate([
            'file' => 'required|mimes:xlsx,xls' 
        ]);
        // Process the uploaded Excel file
        Excel::import(new ItemImport, $request->file('file'));

        return response()->json(['message' => 'Bulk upload successful'], 201);
    }

    public function exportCategories()
    {
        // dd('test');
        return Excel::download(new CategoryExport, 'categories.xlsx');
    }

    public function getItemsBySectionId(Request $request, $section_id)
    {
        $user = Auth::user();

        $section = Section::findOrFail($section_id);

        $items = $section->items()->with(['modifierGroups', 'sectionWisePricings'])->get();
        // $items = Item::where('restaurant_id', $user->restaurant_id)->with(['modifierGroups', 'sectionWisePricings'])->get();

        $items->each(function ($item) use ($section_id) {
            $item->section_id = $section_id;
        });

        $items = ItemSectionPriceResource::collection($items);
        
        return response()->json(['success' => true, 'data' => $items]); 
    }

    public function getCategoryItemsBySectionId(Request $request, $category_id, $section_id)
    {
        $user = Auth::user();

        $category = Category::with(['items.modifierGroups', 'items.sectionWisePricings'])->where("category_id", $category_id)->first();
        $items = $category->items;

        // $items = Item::where('restaurant_id', $user->restaurant_id)->with(['modifierGroups', 'sectionWisePricings'])->get();

        $items->each(function ($item) use ($section_id) {
            $item->section_id = $section_id;
        });

        $items = ItemSectionPriceResource::collection($items); //->additional(["section_id"=>$section_id]);// This is for collection 

        // $items->extra = (object) ['section_id' => $section_id]; //& This is the second way , But for single object

        // $items = new ItemSectionPriceResource($items,$section_id); //! This is the third way, but for single object

        return response()->json(['success' => true, 'data' => $items]);
    }

}
