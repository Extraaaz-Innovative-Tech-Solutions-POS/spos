<?php

namespace App\Http\Resources;

use App\Models\TableActive;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class TableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function tablesData($tables_count, $floor_number, $section_id)
    {
        $data = [];
        $count = $tables_count;

        while($count > 0)
        {
            $tableActive = TableActive::where('restaurant_id',$this->restaurant_id)
                                ->where('floor_number',$floor_number)
                                ->where('section_id',$section_id)
                                ->where("table_number",$count)
                                ->get()->groupBy('table_number')->first();

            // dump($tableActives? $tableActives->toArray() : null);
            $data["table_number $count"] = $tableActive ? $tableActive->toArray() : null;
            // $data = Arr::add($data, $count, $tableActive);
            $count--;
        }

        // dump($data);
        return $data;
    }

    public function toArray($request)
    {
        return [
            'floor_number' => $this->floor_number,
            'section_id' => $this->section_id,
            'section_name' => $this->sections->name,
            'tables' => $this->tables,
            'tables_data' => $this->tablesData($this->tables, $this->floor_number, $this->section_id),
        ];
    }
}
