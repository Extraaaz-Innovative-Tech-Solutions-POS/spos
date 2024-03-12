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
        'name',
        'is_cancelled',
        'status',
        'cart_id',
        'restaurant_id',
        'created_at',
        'updated_at',
        'cancel_reason',
    ];

    public function kot()
    {
        return $this->belongsTo(KOT::class, 'kot_id');
    }
}
