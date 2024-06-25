<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
{ /**
    * Transform the resource into an array.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
    */

   public function totalAmountPaid($paymentsData)
   {
       $totalAmountPaid = 0;
       foreach ($paymentsData as $payment) {
           $totalAmountPaid += $payment->amount_paid;   
       }
       return $totalAmountPaid;
   }
   public function toArray($request)
   {
       // return parent::toArray($request);

       return [
           'id' => $this->id,
           'supplier_name' => $this->supplier ? $this->supplier->name : null,
           'restaurant_id' => $this->restaurant_id,
           'loc_id' => $this->loc_id,
           'supplier_id' => $this->supplier_id,
           'invoice_number' => $this->invoice_number,
           'product_name' => $this->product_name,
           'unit' => $this->unit,
           'quantity' => $this->quantity,
           'rate' => $this->rate,
           'cgst' => $this->cgst,
           'sgst' => $this->sgst,
           'vat' => $this->vat,
           'tax' => $this->tax,
           'amount' => $this->amount,
           'discount' => $this->discount,
           'net_payable' => $this->net_payable,
           'created_at' => $this->created_at,
           'updated_at' => $this->updated_at,
           'deleted_at' => $this->deleted_at,
           'amount_paid' => $this->purchaseOrderPayments ? $this->totalAmountPaid($this->purchaseOrderPayments) : null,
           'payment_type' => $this->latestPurchaseOrderPayment ? $this->latestPurchaseOrderPayment->payment_type : null,
           'purchase_order_status' => $this->latestPurchaseOrderPayment ? $this->latestPurchaseOrderPayment->status : null,
           'payments' => $this->purchaseOrderPayments ? $this->purchaseOrderPayments : [],
           "returns" => $this->returnOrders ? $this->returnOrders : [],
       ];
   }
}
