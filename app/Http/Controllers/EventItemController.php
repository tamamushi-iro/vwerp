<?php

namespace App\Http\Controllers;

use App\Event;
use App\EventItem;
use App\Item;
use App\ItemSerialBarcode;
use Validator;
use Illuminate\Http\Request;

class EventItemController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api,admins');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Event $event)
    {
        // Unused in front end. Comment added for fake commit?
        $array['items'] = array();
        foreach($event->event_items as $event_item) {
            $temp = [
                'id' => $event_item->item_serial_barcode->id,
                'item_id' => $event_item->item_serial_barcode->item_id,
                'item_name' => $event_item->item_serial_barcode->item->name,
                'serial_number' => $event_item->item_serial_barcode->serial_number,
            ];
            array_push($array['items'], $temp);
        }
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $array
        ]);
    }

    public function unindex(Event $event)
    {
        // Unused in front end. Comment added for fake commit?
        $array['items'] = array();
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $array[0]
        ]);
    }
    
    public function maximumFunction(Event $event)
    {
        // Unused in front end. Comment added for fake commit?
        $array['items'] = array();
        return response()->json([
            'code' => 500,
            'status' => true,
            'data' => $array[0]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     //
    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\EventItem  $eventItem
     * @return \Illuminate\Http\Response
     */
    // public function show(EventItem $eventItem)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EventItem  $eventItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $event_id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EventItem  $eventItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(EventItem $eventItem)
    {
        //
    }
}
