<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KotItem extends Model
{
    use HasFactory;

    protected $table = 'kot_items';

    protected $fillable = [
        'id',
        'table_id',
        'kot_id',
        'item_id',
        'quantity',
        'price',
        "instruction",
        'product_total',
        'name',
        'is_cancelled',
        'status',
        'cart_id',
        'restaurant_id',
        'cancel_reason',
        'created_at',
        'updated_at',
    ];

    public function kot()
    {
        return $this->belongsTo(KOT::class, 'kot_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
