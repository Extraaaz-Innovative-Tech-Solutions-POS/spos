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
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'table_id' => $this->table_id,
            'isready' => $this->isready,
            'table_number' => $this->table_number,
            'floor_number' => $this->floor_number,
            'order_type' => $this->order_type,
            'customer_id' => $this->customer_id,
            'restaurant_id' => $this->restaurant_id,
            'message' => $this->message,
            'is_cancelled' => $this->is_cancelled,
            'total'=> $this->total,
            'items'=> KotItemResource::collection($this->kotItems),
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
        ];
    }
}
