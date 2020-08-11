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
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Event $event)
    {
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
        $validator = Validator::make($request->all(), [
            'serial_number' => 'required|array',
            'serial_number.*' => 'distinct|string|exists:item_serial_barcodes,serial_number'
        ], [
            'serial_number.*.exists' => 'Serial number does not exist'
        ]);

        if($validator->fails()) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        } else {
            foreach($request['serial_number'] as $serial) {
                $itemSerialBarcode = ItemSerialBarcode::where('serial_number', $serial)->first();
                // Following condition should not occur if front-end never has non-available serials
                if(!$itemSerialBarcode->is_available) {
                    return response()->json([
                        'code' => 400,
                        'status' => false,
                        'message' => 'Serial number is already assigned to an event'
                    ]);
                } else {
                    $item = Item::find($itemSerialBarcode->item_id);
                    // Update available_quantity in items and set is_available to false in itemserialbarcode
                    $itemSerialBarcode->is_available = false;
                    $itemSerialBarcode->save();
                    if($item->available_quantity > 0) {
                        $item->available_quantity--;
                        $item->save();
                    } else {
                        return response()->json([
                            'code' => 400,
                            'status' => false,
                            'message' => 'Item: ' . $item->name . ' Serial: ' . $itemSerialBarcode->serial_number . ' not available in sufficient quantity. Available: (' . $item->available_quantity . ', ' . $itemSerialBarcode->available_quantity .')'
                        ]);
                    }
                    // Finally add, i.e. update the items for event
                    $eventItem = EventItem::create(['event_id' => $event_id, 'item_serial_barcode_id' => $itemSerialBarcode->id]);
                }
            }
        }
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Item added to event successfully'
        ]);

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
