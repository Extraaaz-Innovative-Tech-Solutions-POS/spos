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


}
