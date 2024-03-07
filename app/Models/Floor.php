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
        'floor'
    ];
}
