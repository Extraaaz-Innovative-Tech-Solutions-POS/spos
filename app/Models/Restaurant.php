<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;
    protected $table = 'restaurants';
    protected $fillable = [
        'id',
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'district',
        'state',
        'country',
        'pincode',
        'license_id',
        'fssai_id',
        'gst_no',
        'latitude',
        'longitude',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

// 'description',
// 'restaurant_id',