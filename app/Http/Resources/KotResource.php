<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function moneyRemaining($orderPayments)
    {
        $totalMoney = $this->grand_total;
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
        // return parent::toArray($request);
        $filteredItems = $this->kotItems()->where('is_cancelled', 0)->get();
        return [
            'id' => $this->id,
            'table_id' => $this->table_id,
            'order_number' => $this->order_number,
            'isready' => $this->isready,
            'table_number' => $this->table_number,
            'floor_number' => $this->floor_number,
            'order_type' => $this->order_type,
            'customer_id' => $this->customer_id,
            'customer' => new CustomerResource($this->customer),
            'restaurant_id' => $this->restaurant_id,
            'message' => $this->message,
            'status' => $this->status,
            'is_cancelled' => $this->is_cancelled,
            'cancelled_reason' => $this->cancelled_reason,
            'advance_order_date_time' => $this->advance_order_date_time,
            'delivery_address' => $this->delivery_address,
            'delivery_status' => $this->delivery_status,
            'total'=> $this->total,
            'total_discount' => $this->total_discount,
            'total_tax' => $this->total_tax,
            'grand_total' => $this->grand_total,
            'cgst_tax'=>$this->cgst_tax,
            'sgst_tax'=>$this->sgst_tax,
            'vat_tax'=>$this->vat_tax,
            'total_given_amount' => $this->orderPayments ? $this->totalAmountGiven($this->orderPayments) : 0,
            'remaining_money' => $this->orderPayments ? $this->moneyRemaining($this->orderPayments) : 0,
            'items'=> KotItemResource::collection($filteredItems),
            'payments' => $this->orderPayments, // ? $this->order_payments : null,
            
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
        ];
    }
}
