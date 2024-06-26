<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'id','product_name','quantity','recipe_id','restaurant_id','unit',
        // Add other columns as needed
    ];

    // protected $guarded = [];

    // protected $casts = [
    //     'recipe_id' => 'array',
    // ];
}
