<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
