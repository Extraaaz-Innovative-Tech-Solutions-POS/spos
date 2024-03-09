<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'category_id '=> $this->category_id,
            'category_name'=> $this->category_name,
            'restaurant_id '=> $this->restaurant_id,
            'descirption'=> $this->descirption,
            'items' => ItemResource::collection($this->items),
        ];
    }
}
