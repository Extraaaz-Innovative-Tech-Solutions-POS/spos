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
    public function sectionWiseItems()
    {
        $items = ItemSectionPriceResource::collection($this->whenLoaded('items'));
        $section_id = $this->section_id;
        $items->each(function ($item) use ($section_id) {
            $item->section_id = $section_id;
        });
        return $items;
    }
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'category_id '=> $this->category_id,
            'category_name'=> $this->category_name,
            'restaurant_id '=> $this->restaurant_id,
            'description'=> $this->description,
            'section_id'=> $this->section_id,
            'items' => $this->sectionWiseItems(), //ItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
