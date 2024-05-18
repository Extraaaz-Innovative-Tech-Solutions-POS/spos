<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemSectionPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function sectionWisePrice($collection)
    {
        // $section_id = $this->additional['section_id'] ?? 14;
        $section_id = $this->section_id;
        $sectionWisePrice = $collection->where('section_id', $section_id)->first();
        $price = $sectionWisePrice ? $sectionWisePrice->price : null;
        $price = $price ? strval($price) : null;
        return $price;
    }
    public function toArray($request)
    {
        $section_id = $this->section_id;//additional['section_id'][0] ?? null;
        return  [
            'id' => $this->id,
            'name' => $this->item_name,
            'actual_price' => $this->price,
            'price' => $this->section_id ? ($this->sectionWisePrice($this->sectionWisePricings) ?? $this->price) : $this->price, //$this->section_id ? ($this->sectionWisePricings ? $this->sectionWisePrice($this->sectionWisePricings) : $this->price ) : $this->price,
            'discount' => $this->discount,
            'category_id' => $this->category_id,
            'category_name' => $this->category ? $this->category->category_name : null,
            'restaurant_id' => $this->restaurant_id,
            'food_type' => $this->food_type,
            'associated_item' => $this->associated_item,
            'tax_percentage' => $this->tax_percentage,
            'short_code' => $this->short_code,
            'section_id' => $section_id ?? null,
            // 'sectionWisePricings' => ItemPricingResource::collection($this->whenLoaded('sectionWisePricings')),
            'modifierGroups' => $section_id ? ModifierGroupResource::collection($this->whenLoaded('modifierGroups'))->where('section_id', '=', $this->section_id)->values()
            : ModifierGroupResource::collection($this->whenLoaded('modifierGroups'))->values(),
        ];
    }
}
