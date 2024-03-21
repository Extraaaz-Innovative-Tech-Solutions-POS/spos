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
        'floor_id',
        'restaurant_id',
        'floor_number',
        'section_id',
        'tables',
    ];

    public function sections()
    {
        return $this->belongsTo(Section::class,'section_id');
    }

}
