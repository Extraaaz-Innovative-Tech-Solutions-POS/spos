<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'customer';
    // protected $primaryKey = 'category_id';
    protected $fillable=[
        'id',
        'name',
        'address',
        'phone',
        'restaurant_id'
    ];

    public function customer_address() {
        return $this->hasMany(CustomerAddress::class, 'customer_id','id');
    }

}
