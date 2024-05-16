<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ModifierGroupResource extends JsonResource
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
            'user_id' => $this->user_id,
            'user_name' => $this->user ? $this->user->name : null, 
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type == 1 ? 'add-ons' : 'variants',
            'section_id' => $this->section_id,
            'section_name' => $this->section ? $this->section->name : null,
            'restaurant_id' => $this->restaurant_id,
            'items_count' => $this->items ? $this->items->count() : null,
            'modifiers_count' => $this->modifiers ? $this->modifiers->count() : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'modifiers'=> ModifierResource::collection($this->whenLoaded('modifiers')),
            // 'items' => ItemResource::collection($this->whenLoaded('items')),
            // 'deleted_at' => $this->deleted_at,
        ];
    }
}
