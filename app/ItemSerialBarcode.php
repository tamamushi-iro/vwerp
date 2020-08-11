<?php

namespace App;

use App\Item;
use Illuminate\Database\Eloquent\Model;

class ItemSerialBarcode extends Model
{
    protected $fillable = [
        'item_id', 'serial_number', 'qrcode_path', 'is_available'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function item() {
        return $this->belongsTo(Item::class);
    }

}
