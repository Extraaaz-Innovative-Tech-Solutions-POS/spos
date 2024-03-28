<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FloorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'restaurant_id' => $this->restaurant_id,
            'floor_name' => $this->floor_name,
            'sections' => $this->sections ? SectionResource::collection($this->sections) : null,
        ];
    }
}
