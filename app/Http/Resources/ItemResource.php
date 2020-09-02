<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\EventItem;
use Throwable;

class ItemResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $array = parent::toArray($request);
        // TO BE REMOVED: OR NOT?
        try {
            $array['class'] = strtoupper($this->tagsClass->tag_name);
        } catch (Throwable $e) {
            $array['class'] = 'UNKNOWN';
        }
        try {
            $array['category'] = strtoupper($this->tagsCategory->tag_name);
        } catch (Throwable $e) {
            $array['category'] = 'UNKONWN';
        }
        try {
            $array['type'] = strtoupper($this->tagsType->tag_name);
        } catch (Throwable $e) {
            $array['type'] = 'UNKNOWN';
        }

        if(isset($request['show']) and $request['show'] == 'available') {
            foreach($this->item_serial_barcodes as $itemSerialBarcode) {
                if($itemSerialBarcode->is_available) {
                    $array['serials'][] = $itemSerialBarcode;
                }
            }
        } else {
            foreach($this->item_serial_barcodes as $itemSerialBarcode) {
                $eventItem = EventItem::where('item_serial_barcode_id', $itemSerialBarcode->id)->first();
                if(!is_null($eventItem)) {
                    $array['serials'][] = array_merge($itemSerialBarcode->toArray(), [
                        'event_id' => $eventItem->event_id,
                        'event_name' => $eventItem->events->name,
                        'assigned_quantity' => $eventItem->assigned_quantity
                    ]);
                } else {
                    $array['serials'][] = array_merge($itemSerialBarcode->toArray(), [
                        'event_id' => NULL,
                        'event_name' => NULL,
                        'assigned_quantity' => NULL
                    ]);
                }
            }
        }
        return $array;
    }
}

// this whole file gives me da creeps