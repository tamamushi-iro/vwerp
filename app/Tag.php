<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'tag_name', 'tag_type'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
