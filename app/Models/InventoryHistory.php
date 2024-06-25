<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    use HasFactory;
    protected $table = 'inventory_history';

    protected $fillable = [
        'product_name',
        'qty_change',
        'qty_remaining',
        'change_type',
        'reason',
        'restaurant_id',
        'loc_id'
    ];
}
