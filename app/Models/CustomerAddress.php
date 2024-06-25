<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'id ',
        'user_id',
        'customer_id',
        'type',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'restaurant_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
