<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    // protected $primaryKey = 'order_id';
    protected $fillable = [
        'id',
        'table_id',
        'ispaid',
        'table_number',
        'floor_number',
        'order_type',
        'customer_id',
        'invoice_id',
        'restaurant_id',
        'product',
        'product_total',
        'total_discount',
        'subtotal',
        'restrotaxtotal',
        'restro_tax',
        'othertaxtotal',
        'other_tax',
        'total',


    ];
}
