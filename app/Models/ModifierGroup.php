<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModifierGroup extends Model
{
    use HasFactory;

    protected $table = 'modifiergroups';

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

    public function modifiers()
    {
        return $this->belongsToMany(Modifier::class,'modifiergroup_modifier','modifiergroup_id','modifier_id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_modifiergroup','modifiergroup_id','item_id');
    }
}
