<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPricing extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'item_id',
        'section_id',
        'price',
        'user_id',
        'restaurant_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];


    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function section()
    {
        return $this->belongsTo(Section::class,'section_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class,'item_id');
    }
}
