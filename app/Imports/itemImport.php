<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Item; // Replace YourModel with the name of your model
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemImport implements ToModel,  WithHeadingRow
{
    // public function collection(Collection $rows)
    // {
    public function model(array $row)
    {
        $user = Auth::user(); 
        $restaurant_id = $user->restaurant_id;
        
        $item = new Item([
            'item_name' => $row['item_name'],
            'price' => $row['price'],
            'food_type' => $row['food_type'],
            'category_id' => $row['category_id'],
            'restaurant_id' => $restaurant_id
        ]);

        dd($item);

        return $item;

    }
}
