<?php

namespace App\Imports;

use App\Models\ModifierGroup;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ModifierGroupImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $user = Auth::user();

        $modifierGroup = new ModifierGroup([
            'user_id' => $user->id,
            'name' => $row['name'],
            'description' => $row['description'],
            'type' => $row['type'],
            'restaurant_id' => $user->restaurant_id,
        ]);

        return $modifierGroup;
    }
}
