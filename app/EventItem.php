<?php

namespace App;

use App\Event;
use App\ItemSerialBarcode;
use Illuminate\Database\Eloquent\Model;

class EventItem extends Model
{
    protected $fillable = [
        'event_id', 'item_serial_barcode_id', 'assigned_quantity'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function events() {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    public function item_serial_barcode() {
        return $this->hasOne(ItemSerialBarcode::class, 'id', 'item_serial_barcode_id');
    }

}
