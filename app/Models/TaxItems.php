<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxItems extends Model
{
    use HasFactory;
    protected $table = 'tax_items';

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
