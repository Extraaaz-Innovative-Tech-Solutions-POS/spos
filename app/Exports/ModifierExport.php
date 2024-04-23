<?php

namespace App\Exports;

use App\Models\Modifier;
use Maatwebsite\Excel\Concerns\FromCollection;

class ModifierExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Modifier::all();
    }
}
