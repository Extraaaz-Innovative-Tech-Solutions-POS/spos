<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRestaurant extends Model
{
    use HasFactory;
    protected $table = 'tax_restaurants';

    protected $fillable = [
        'id',
        'tax_id',
        'item_id',
        'percentage',
        'is_active',
        'created_at',
        'updated_at'

    ];
}
