<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LedCabinet extends Model
{
    protected $fillable = [
        'item_id', 'serial_number', 'qrcode_path',
        'is_lost', 'is_available', 'in_maintenance'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function item() {
        return $this->belongsTo(Item::class);
    }
}
