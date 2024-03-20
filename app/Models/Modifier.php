<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modifier extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'name',
        // 'type',
        'short_name',
        'description',
        'price',
        'restaurant_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
