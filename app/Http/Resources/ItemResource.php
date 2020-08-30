<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;

class ItemResource extends JsonResource
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
        // TO BE REMOVED:
        try {
            $array['class'] = $this->tagsClass->tag_name;
        } catch (Throwable $e) {
            $array['class'] = 'Unknown';
        }
        try {
            $array['category'] = $this->tagsCategory->tag_name;
        } catch (Throwable $e) {
            $array['category'] = 'Unknown';
        }
        try {
            $array['type'] = $this->tagsType->tag_name;
        } catch (Throwable $e) {
            $array['type'] = 'Unknown';
        }
        if(isset($request['show']) and $request['show'] == 'available') {
            foreach($this->item_serial_barcodes as $itemSerialBarcode) {
                if($itemSerialBarcode->is_available) {
                    $array['serials'][] = $itemSerialBarcode;
                }
            }
        } else {
            $array['serials'] = $this->item_serial_barcodes;
        }
        return $array;
    }
}
