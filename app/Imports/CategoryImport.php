<?php

namespace App\Imports;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CategoryImport implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        $user = Auth::user(); 
        $restaurant_id = $user->restaurant_id;
        
        $category = new Category([
            'restaurant_id ' => $restaurant_id,
            'category_name' => $row['category_name'],
            'description' => $row['description'],
        ]);
    }
}
