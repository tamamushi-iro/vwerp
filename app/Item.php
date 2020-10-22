<?php

namespace App;

use App\ItemSerialBarcode;
use App\Tag;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name', 'total_quantity', 'available_quantity', 'class', 'category', 'type', 'item_type_code'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function item_serial_barcodes() {
        return $this->hasMany(ItemSerialBarcode::class);
    }

    public function tagsClass() {
        return $this->hasOne(Tag::class, 'id', 'class');
    }

    public function tagsCategory() {
        return $this->hasOne(Tag::class, 'id', 'category');
    }

    public function tagstype() {
        return $this->hasOne(Tag::class, 'id', 'type');
    }
}
