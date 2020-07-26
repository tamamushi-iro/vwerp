<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{

    protected $fillable = [
        'name', 'start_date', 'end_date', 'reporting_date', 'location',
        'client_name', 'client_phone', 'client_company',
        'technician_name', 'technician_details', 'vehicle_number', 'driver_name', 'driver_phone',
        'invoice_number', 'priority'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     'start_date' => 'datetime',
    //     'end_date' => 'datetime',
    //     'reporting_date' => 'datetime'
    // ];
}
