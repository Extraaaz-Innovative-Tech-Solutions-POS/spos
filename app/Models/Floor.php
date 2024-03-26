<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    use HasFactory;
    protected $table = 'floors';
    // protected $primaryKey = 'category_id';

    protected $fillable = [
        'id ',
        'restaurant_id',
        'floor_name'
    ];

    // public function tables()
    // {
    //     return $this->hasManyThrough(Tables::class, FloorSection::class, 'floor_id', 'floor_section_id');
    // }

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'floor_section', 'floor_id', 'section_id')->withPivot('tables_count')->withTimestamps();
    }

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

}
