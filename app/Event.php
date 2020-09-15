<?php

namespace App;

use App\EventItem;
use App\EventItemsHistory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model {

    protected $fillable = [
        'invoice_number',
        'name', 'start_date', 'end_date', 'reporting_date', 'location',
        'client_name', 'client_email', 'client_phone', 'client_company',
        'technician_name', 'technician_phone', 'vehicle_number', 'driver_name', 'driver_phone',
        'priority',
        'has_ended', 'is_final', 'mail_sent'
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

    public function event_items() {
        return $this->hasMany(EventItem::class);
    }

    public function event_items_history() {
        return $this->hasMany(EventItemsHistory::class);
    }

}
