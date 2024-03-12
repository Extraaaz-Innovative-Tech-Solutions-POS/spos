<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KotItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return //parent::toArray($request);
            [
                'id' => $this->id,
                'table_id' => $this->table_id,
                'kot_id' => $this->kot_id,
                'item_id' => $this->item_id,
                'quantity' => $this->quantity,
                'price' => $this->price,
                'name' => $this->name,
                'product_total'=>$this->product_total,
                'is_cancelled' => $this->is_cancelled,
                'status' => $this->status,
                // 'cart_id' => $this->cart_id,
                'restaurant_id' => $this->restaurant_id,
                // 'created_at' => $this->created_at,
                // 'updated_at' => $this->updated_at,
                'cancel_reason' => $this->cancel_reason,
            ];
    }
}
