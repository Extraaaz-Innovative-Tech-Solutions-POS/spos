<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'supplier_name',
        'restaurant_id',
        'loc_id',
        'supplier_id',
        'invoice_number',
        'product_name',
        'unit',
        'original_quantity',
        'quantity',
        'rate',
        'cgst',
        'sgst',
        'vat',
        'tax',
        'amount',
        'discount',
        'net_payable',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }

    // public function purchaseOrderPayments()
    // {
    //     return $this->hasMany(PurchaseOrderPayment::class,'purchase_order_id');
    // }

    // public function latestPurchaseOrderPayment()
    // {
    //     return $this->hasOne(PurchaseOrderPayment::class,'purchase_order_id')->latest();//OfMany();
    // }

    // public function returnOrders()
    // {
    //     return $this->hasMany(InventoryReturnProduct::class,'purchase_order_id');
    // }

}
