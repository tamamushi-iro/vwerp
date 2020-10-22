<?php

namespace App;

use App\Item;
use Illuminate\Database\Eloquent\Model;

class ItemSerialBarcode extends Model
{
    protected $fillable = [
        'item_id', 'serial_number', 'qrcode_path', 'total_quantity', 'available_quantity', 'lost_quantity',
        'is_lost', 'is_available', 'in_maintenance', 'notes'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function item() {
        return $this->belongsTo(Item::class);
    }

}
