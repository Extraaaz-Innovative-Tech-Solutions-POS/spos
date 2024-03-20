<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModifierGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'name',
        'description',
        'type',
        'restaurant_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
