<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

     public function moneyRemaining($orderPayments)
     {
         $totalMoney = $this->total;
         $paidMoney = 0;
         foreach ($orderPayments as $orderPayment) {
             $paidMoney += $orderPayment->money_given;
         }
 
        return $totalMoney - $paidMoney;
     }
 
     public function totalAmountGiven($orderPayments)
     {
         $paidMoney = 0;
         foreach ($orderPayments as $orderPayment) {
             $paidMoney += $orderPayment->money_given;
         }
 
         return $paidMoney;
     }


    public function toArray($request)
    {
        $filteredItems = $this->kotItems()->where('is_cancelled', 0)->get();
        return [
            
            'id' =>$this-> id,
            'table_id' =>$this->table_id,
            'ispaid' =>$this->ispaid,
            'sub_table_number' =>$this->sub_table_number,
            'table_number' =>$this->table_number,
            'section_id' =>$this->section_id,
            'floor_number' =>$this->floor_number,
            'order_type' =>$this->order_type,
            'customer_id' =>$this->customer_id,
            'invoice_id' =>$this->invoice_id,
            'restaurant_id' =>$this->restaurant_id,
           // 'product' =>$this->product,
            'product_total' =>$this->product_total,
            'total_discount' =>$this->total_discount,
            'subtotal' =>$this->subtotal,
            'restrotaxtotal' =>$this->restrotaxtotal,
            'restro_tax' =>$this->restro_tax,
            'othertaxtotal' =>$this->othertaxtotal,
            'other_tax' =>$this->other_tax,
            'total' =>$this->total,
            'status' =>$this->status,
            'no_of_thali' =>$this->no_of_thali,
            'thali_price' =>$this->thali_price,
            'order_date' => $this->advance_order_date_time,
            'total_given_amount' => $this->orderPayments ? $this->totalAmountGiven($this->orderPayments) : 0,
            'remaining_money' => $this->orderPayments ? $this->moneyRemaining($this->orderPayments) : 0,
            "payments" => $this->orderPayments,
            'kotItems'=>$this->kotItems,
            'customer'=>$this ->customer,



        ];
    }
}
