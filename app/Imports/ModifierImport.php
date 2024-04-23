<?php

namespace App\Imports;

use App\Models\Modifier;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ModifierImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $user = Auth::user();

        return new Modifier([
            'user_id' => $user->id,
            'name' => $row['name'],
            // 'type' => $row['type'],
            'short_name' => $row['short_name'],
            'description' => $row['description'],
            'price' => $row['price'],
            'restaurant_id' => $user->restaurant_id,
        ]);

    }
}
