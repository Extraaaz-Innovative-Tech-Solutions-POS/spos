<?php

namespace App\Exports;

use App\Models\Modifier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ModifierExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Modifier::all();
    }

    public function headings(): array
    {
        return [
            'id',
            'user_id',
            'name',
            'type',
            'short_name',
            'description',
            'price',
            'restaurant_id',
            'created_at',
            'updated_at',
        ];
    }
}
