<?php

namespace App\Exports;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoryExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $restaurant_id = Auth::user()->restaurant_id;

        return Category::select('category_id', 'category_name')->where('restaurant_id',$restaurant_id)->get();
    }

    public function headings(): array
    {
        return [
            'Category ID',
            'Category Name',
        ];
    }
}
