<?php

namespace App\Http\Resources;

use App\Models\TableActive;
use Illuminate\Http\Resources\Json\JsonResource;

class TableActiveResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    // public static $wrap = 'table_number';

    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'floor_id' => $this->floor_number ?? null,
            'section_id' => $this->section_id ?? null,
            'table_number'=>$this->table_number ?? null,
            // 'sub_table' => $this->split_table_number ?? null,
            'divided_by' => $this->divided_by ?? null,
            'table_data'=>TableActive::where('restaurant_id',$this->restaurant_id)->where("table_number",$this->table_number)
                                ->get()->groupBy('table_number')->first(),
        ];
    }
}
