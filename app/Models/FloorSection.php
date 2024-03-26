<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FloorSection extends Model
{
    use HasFactory;

    protected $table = 'floor_section';
    
    // protected $guarded = [];

    protected $fillable = [
        'id',
        'user_id',
        'floor_id',
        'section_id',
        'restaurant_id',
        'created_at',
        'updated_at',
    ];


    // public function tables()
    // {
    //     return $this->hasMany(Tables::class, 'floor_section_id');
    // }

    public function section()
    {
        return $this->belongsTo(Section::class,'section_id');
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class, 'floor_id');
    }
}
