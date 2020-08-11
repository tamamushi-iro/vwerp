<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $array = parent::toArray($request);
        // $array['items'] = $this->event_items
        $array['items'] = array();
        foreach($this->event_items as $event_item) {
            $temp = [
                'id' => $event_item->item_serial_barcode->id,
                'item_id' => $event_item->item_serial_barcode->item_id,
                'item_name' => $event_item->item_serial_barcode->item->name,
                'serial_number' => $event_item->item_serial_barcode->serial_number,
            ];
            array_push($array['items'], $temp);
        }
        return $array;
    }
}
