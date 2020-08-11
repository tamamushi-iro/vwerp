<?php

namespace App;

use App\ItemSerialBarcode;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name', 'total_quantity', 'available_quantity', 'class', 'category', 'type'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function item_serial_barcodes() {
        return $this->hasMany(ItemSerialBarcode::class);
    }

}
