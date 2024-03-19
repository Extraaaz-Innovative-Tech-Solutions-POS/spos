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
        'description'
    ];
    public function locations()
    {
        return $this->belongsToMany(Location::class)->withTimestamps();
    }

    public function items()
    {
        return $this->hasMany(Items::class, 'category_id');
    }
}
