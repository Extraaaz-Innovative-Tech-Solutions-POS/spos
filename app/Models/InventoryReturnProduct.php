<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryReturnProduct extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public $casts = [
        // "created_at" => "datetime:Y-m-d H:i:s"
    ];

    public function purchase_order()
    {
        return $this->belongsTo(PurchaseOrder::class,'purchase_order_id');
    }
}
