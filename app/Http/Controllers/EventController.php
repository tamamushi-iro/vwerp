<?php

namespace App\Http\Controllers;

use App\Event;
use App\Item;
use App\EventItem;
use App\Http\Resources\EventResource;
use App\ItemSerialBarcode;
use Validator;
use Illuminate\Http\Request;

class EventController extends Controller
{

    public function __construct() {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $eventResource = EventResource::collection(Event::all());
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $eventResource
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'reporting_date' => 'required|date',
            'location' => 'required',
            'client_name' => 'required',
            'client_phone' => 'required|regex:/^[0-9]{10}$/',
            'client_company' => 'required',
            'technician_name' => 'required',
            'technician_details' => 'required',
            'vehicle_number' => 'required',
            'driver_name' => 'required',
            'driver_phone' => 'required|regex:/^[0-9]{10}$/',
            'invoice_number' => 'string',
            'priority' => 'string',
            'serial_number' => 'array',
            'serial_number.*' => 'distinct|string|exists:item_serial_barcodes,serial_number'
            // 'invoice_number' => 'required|unique:events'
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
            $event = Event::create($validator->validated());
            // Items for events are added here
            if(isset($request['serial_number'])) {
                $data = $this->addEventItems($request, $event['id']);
                if(!$data['status']) {
                    $event->delete();
                    return response()->json($data);
                }
            }
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Event Created successfully'
            ]);
        }
    }

    // Function for store and update functions
    protected function addEventItems(Request $request, $event_id) {
        foreach($request['serial_number'] as $serial) {
            $itemSerialBarcode = ItemSerialBarcode::where('serial_number', $serial)->first();
            // Following condition should not occur if front-end never has non-available serials
            if(!$itemSerialBarcode->is_available) {
                return array(
                    'code' => 400,
                    'status' => false,
                    'message' => 'Serial number:'. $serial .' is already assigned to an event'
                );
            } else {
                $item = Item::find($itemSerialBarcode->item_id);
                // Update available_quantity in items and set is_available to false in itemserialbarcode
                $itemSerialBarcode->is_available = false;
                $itemSerialBarcode->save();
                // REDUNDENT CONDITION? check back after flow diagram is complete, should never execute?
                if($item->available_quantity > 0) {
                    $item->available_quantity--;
                    $item->save();
                } else {
                    return array(
                        'code' => 400,
                        'status' => false,
                        'message' => 'Item: ' . $item->name . ' Serial: ' . $itemSerialBarcode->serial_number . ' not available in sufficient quantity. Available: (' . $item->available_quantity . ')'
                    );
                }
                $eventItem = EventItem::create(['event_id' => $event_id, 'item_serial_barcode_id' => $itemSerialBarcode->id]);
            }
        }
        return array(
            'code' => 200,
            'status' => true,
            'message' => 'Event Created/Updated successfully'
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event) {
        $eventResource = new EventResource($event);
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $eventResource
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event) {
        $validator = Validator::make($request->all(), [
            'serial_number' => 'array',
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
            $event->update($request->all());
            if(isset($request['serial_number'])) {
                $data = $this->addEventItems($request, $event['id']);
                if(!$data['status']) return response()->json($data);
            }
        }
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Event Updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event) {
        $event->delete();
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Event Deleted successfully'
        ]);
    }
}
