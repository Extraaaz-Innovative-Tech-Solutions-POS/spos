<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KOT extends Model
{
    use HasFactory;

    protected $table = 'kot';

    protected $fillable = [
        'id',
        'table_id',
        'order_number',
        'isready',
        'sub_table_number',
        'table_number',
        'section_id',
        'floor_number',
        'order_type',
        'customer_id',
        'restaurant_id',
        'message',
        'status',
        'isready',
        'is_cancelled',
        'cancelled_reason',
        'total',
        'total_discount',
        'total_tax',
        'grand_total',
        'advance_order_date_time',
        'delivery_address_id',
        'delivery_status',
        'created_at',
        'updated_at',
    ]; 

    public function kotItems()
    {
        return $this->hasMany(KotItem::class,'kot_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderPayments()
    {
        return $this->hasMany(OrderPayment::class,'table_id','table_id'); //~ 1st Table_id  is of OrderPayment table and 2nd is of KOT table.
    }

    public function delivery_address()
    {
        return $this->belongsTo(CustomerAddress::class,'delivery_address_id');
    }
}
