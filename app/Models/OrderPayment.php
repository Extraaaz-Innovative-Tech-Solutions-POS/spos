<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'order_id',
        'table_id',
        'customer_id',
        'order_number',
        'restaurant_id',
        'payment_type',
        'payment_method',
        'amount',
        'money_given',
        'is_partially_paid',
        'is_full_paid',
        'status',
        'transaction_id',
        'payment_details',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function kot()
    {
        return $this->belongsTo(KOT::class,'table_id');
    }
}
