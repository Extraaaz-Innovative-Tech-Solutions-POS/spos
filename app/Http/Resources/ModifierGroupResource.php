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
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'restaurant_id' => $this->restaurant_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'modifiers'=> ModifierResource::collection($this->modifiers),
            // 'deleted_at' => $this->deleted_at,
        ];
    }
}
