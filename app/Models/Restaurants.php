<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurants extends Model
{
    use HasFactory;
    protected $table = 'Restaurants';
    protected $fillable = [
        'id',
        'restaurant_name',
        'address',
        'owner',
        'description',
        'restaurant_id',
        'license_id',
        'restaurant_email',
        'restaurant_phone'
    ];
}
