<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;
    protected $fillable =[
        'id',
        'user_id',
        'name',
        'restaurant_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function floors()
    {
        return $this->belongsToMany(Floor::class, 'floor_section', 'section_id', 'floor_id')->withPivot('tables_count')->withTimestamps();
    }

    // public function tables()
    // {
    //     return $this->hasMany(Tables::class);
    // }

    // public function tables()
    // {
    //     return $this->hasManyThrough(Tables::class, FloorSection::class,
    //         'section_id',
    //         'floor_section_id'
    //     );
    // }

    public function items()
    {
        return $this->belongsToMany(Item::class,'item_pricing','item_id','section_id')->withPivot('price')->withTimestamps();
    }
}
