<?php

namespace App\Exports;

use App\Models\ModifierGroup;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ModifierGroupExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ModifierGroup::all();
    }

    public function headings(): array
    {
        return [
            'id',
            'user_id',
            'name',
            'description',
            'type',
            'restaurant_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }
}
