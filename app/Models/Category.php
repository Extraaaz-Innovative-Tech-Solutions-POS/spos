<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // use HasFactory;

    use HasFactory;
    protected $table = 'category';
    protected $primaryKey = 'category_id';
    protected $fillable = [
        'category_id ',
        'category_name',
        'restaurant_id ',
        'descirption'



    ];
    public function locations()
{
    return $this->belongsToMany(Location::class)->withTimestamps();
}
}
