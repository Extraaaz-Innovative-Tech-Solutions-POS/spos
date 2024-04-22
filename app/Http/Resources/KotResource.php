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
        $totalMoney = $this->total;
        $paidMoney = 0;
        foreach ($orderPayments as $orderPayment) {
            $paidMoney += $orderPayment->money_given;
        }

        return $totalMoney - $paidMoney;
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
            'restaurant_id' => $this->restaurant_id,
            'message' => $this->message,
            'status' => $this->status,
            'is_cancelled' => $this->is_cancelled,
            'cancelled_reason' => $this->cancelled_reason,
            'total'=> $this->total,
            'remaining_money' => $this->orderPayments ? $this->moneyRemaining($this->orderPayments) : 0,
            'items'=> KotItemResource::collection($filteredItems),
            'payments' => $this->orderPayments, // ? $this->order_payments : null,
            
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
        ];
    }
}
