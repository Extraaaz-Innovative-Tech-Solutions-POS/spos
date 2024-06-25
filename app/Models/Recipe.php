<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    
    use HasFactory;
    protected $table = 'recipes';
    protected $fillable = [ 
       'recipe_name','recipe_pos_id','ingredients','deleted_at'
    ];

    protected $casts = [
        'ingredients' => 'array', 
    ];

    // public function recipe()
    // {
    //     return $this->belongsTo(Recipe::class, 'product_name', 'recipe_name');
    // }

    // public function ingredient()
    // {
    //     return $this->belongsTo(Order::class, 'product_id', 'recipie_id');
    // }

    // public function ingredients()
    // {
    //     return $this->hasMany(Ingredient::class);
    // }

    public function item()
    {
        return $this->belongsTo(Item::class,'recipe_pos_id');
    }

}
