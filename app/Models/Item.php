<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $table = 'items';
    protected $primaryKey = 'item_id'; 

    protected $fillable = [
        'item_id ',
        'item_name',
        'price',
        'discount',
        'category_id ',
        'restaurant_id ',
        'food_type',
        'associated_item',
        'varients',
        'tax_percentage'

    ];


    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','item_id');
    }

    public function kotItems()
    {
        return $this->hasMany(KotItem::class,'item_id','item_id');
    }


}