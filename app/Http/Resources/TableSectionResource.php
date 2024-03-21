<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TableSectionResource extends JsonResource
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
            'floor_number' => $this->floor_number,
            'section_id' => $this->section_id,
            'section_name' => $this->sections ? $this->sections->name : null,
            'tables'=>$this->tables
        ];
    }
}
