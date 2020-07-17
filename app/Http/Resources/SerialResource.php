<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SerialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
        // return [
        //     'id' => $this->id(),
        //     'serial_number' => $this->serial_number(),
        //     'item_id' => $this->item_id(),
        //     'item_name' => $this->$item->name(),
        // ];
    }
}

// THIS FILE IS CURRENTY USELESS