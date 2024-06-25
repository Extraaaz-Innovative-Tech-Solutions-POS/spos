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
        'sub_table_number',
        'table_number',
        'section_id',
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
        'no_of_thali',
        'thali_price',
        'status'


    ];

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function kotItems() {
        return $this->hasMany(KotItem::class, 'table_id','table_id');
    }
    public function orderPayments() {
        return $this->hasMany(OrderPayment::class, 'table_id','table_id');
    }

}
