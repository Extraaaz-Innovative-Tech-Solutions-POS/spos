<?php

namespace App\Imports;

use App\Models\ModifierGroup;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;

class ModifierGroupImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $user = Auth::user();

        return new ModifierGroup([
            'user_id' => $user->id,
            'name' => $row['name'],
            'description' => $row['description'],
            'type' => $row['type'],
            'restaurant_id' => $user->restaurant_id,
        ]);
    }
}
