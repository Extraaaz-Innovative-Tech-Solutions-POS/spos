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
        'modifier_group_name',
        'modifier_group_desc',
        'modifier_group_type',
        'restaurant_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
