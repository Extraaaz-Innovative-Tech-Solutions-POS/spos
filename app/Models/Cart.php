<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'cart';
    protected $primaryKey = 'id';

    protected $fillable =[
        'id',
        'complete',
        'created_at',
        'updated_at',
        'table_number',
        'floor_number',
        'order_type',
        'customer_id',
        'restaurant_id',
        
    ];
}
