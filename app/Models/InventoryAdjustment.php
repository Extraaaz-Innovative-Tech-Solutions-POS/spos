<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    use HasFactory;
    protected $fillable = [
        'inventory_id', 'product_name', 'quantity', 'unit', 'reason'];
}
