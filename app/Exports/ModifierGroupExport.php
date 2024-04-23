<?php

namespace App\Exports;

use App\Models\ModifierGroup;
use Maatwebsite\Excel\Concerns\FromCollection;

class ModifierGroupExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ModifierGroup::all();
    }
}
