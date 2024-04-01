<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableActive extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'table_id',
        'table_number',
        'divided_by',
        'split_table_number',
        'section_id',
        'section_name',
        'floor_number',
        'restaurant_id',
        'cover_count',
        'status',
        'created_at',
        'updated_at',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class,'section_id');
    }
}
