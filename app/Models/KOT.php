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
        'isready',
        'table_number',
        'floor_number',
        'order_type',
        'customer_id',
        'restaurant_id',
        'message',
        'is_cancelled',
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
