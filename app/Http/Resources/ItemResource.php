<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
        return 
        [
            'id' => $this->item_id,
            'name' => $this->item_name,
            'price' => $this->price,
            'discount' => $this->discount,
            'category_id' => $this->category_id,
            'restaurant_id ' => $this->restaurant_id,
            'food_type' => $this->food_type,
            'associated_item' => $this->associated_item,
            'varients' => $this->varients,
            'tax_percentage' => $this->tax_percentage,
        ];
    }
}
