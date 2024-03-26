<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tables extends Model
{
    use HasFactory;
    protected $table = 'tables';
    protected $fillable = [
        'id',
        'restaurant_id',
        'floor_section_id',
        'tables_count',
        'created_at',
        'updated_at',
    ];

    // public function sections()
    // {
    //     return $this->belongsTo(Section::class,'section_id');
    // }

    public function floorSection()
    {
        return $this->belongsTo(FloorSection::class,'floor_section_id');
    }

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

}
