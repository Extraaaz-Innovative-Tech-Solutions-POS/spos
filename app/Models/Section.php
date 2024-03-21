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
        'updated_at'
    ];

    public function tables()
    {
        return $this->hasMany(Tables::class);
    }
}
