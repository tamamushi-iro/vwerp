<?php

namespace App\Http\Controllers;

use App\Event;
use App\Item;
use App\EventItem;
use App\Http\Resources\EventResource;
use App\ItemSerialBarcode;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EventController extends Controller {

    public function __construct() {
        $this->middleware('auth:api,admins', [
            'except' => ['show', 'update', 'indexRange', 'indexNotFinal', 'returnedFromEvent']
        ]);
        $this->middleware('auth:whusers,api,admins', [
            'only' => ['show', 'update', 'indexRange', 'indexNotFinal', 'returnedFromEvent']
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if(isset($request['show']) and $request['show'] == 'all') {
            $eventResource = EventResource::collection(Event::all());
        } elseif(isset($request['show']) and $request['show'] == 'ended') {
            $eventResource = EventResource::collection(Event::where('has_ended', true)->get());
        } else {
            $eventResource = EventResource::collection(Event::where('has_ended', false)->get());
        }
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $eventResource
        ]);
    }

    public function indexRange() {
        $eventResource = EventResource::collection(Event::whereDate('start_date', '<=', Carbon::now()->add(15, 'days')->toDateString())->where('has_ended', false)->get());
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $eventResource
        ]);
    }

    public function indexNotFinal() {
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => EventResource::collection(Event::where('is_final', false)->get())
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
            // 'invoice_number' => 'string|unique:events',
            'name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'reporting_date' => 'required|date',
            'location' => 'required',
            'client_name' => 'required',
            'client_email' => 'required|email',
            'client_phone' => 'required',
            'client_company' => 'required',
            'technician_name' => 'string',
            'technician_phone' => 'string',
            'vehicle_number' => 'string',
            'driver_name' => 'string',
            'driver_phone' => 'string',
            'priority' => 'string',
            'serial_number' => 'array',
            'serial_number.*' => 'distinct|string|exists:item_serial_barcodes,serial_number',
            'serial_quantity' => 'array',
            'serial_quantity.*' => 'integer'
        ], [
            'serial_number.*.exists' => 'Serial number does not exist'
        ]);
        // if(count($request['serial_number']) != count($request['serial_quantity'])) {
        //     return response()->json([
        //         'code' => 400,
        //         'status' => false,
        //         'message' => 'serial_number array and serial_quantity array do not match in size'
        //     ]);
        // }

        if($validator->fails()) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        } else {
            // Create Event
            $event = Event::create($validator->validated());
            // Auto-Generate Invoice Number as: VW-MMYYYY-ID
            $date = Carbon::parse(Carbon::now());
            $month = ($date->month < 10) ? "0$date->month" : $date->month;
            $invoiceNo = "VW-$month$date->year-$event->id";
            $event->update(['invoice_number' => $invoiceNo]);
            // Items for events are added here
            if(isset($request['serial_number']) and isset($request['serial_quantity'])) {
                if(count($request['serial_number']) != count($request['serial_quantity'])) {
                    return response()-json([
                        'code' => 400,
                        'status' => false,
                        'message' => "'serial_number' array and 'serial_quantity' array do not match in size"
                    ], 400);
                }
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
        // Check for if event already ended.
        if($event->has_ended) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => 'Event has already ended'
            ], 400);
        }
        $validator = Validator::make($request->all(), [
            'serial_number' => 'array',
            'serial_number.*' => 'distinct|string|exists:item_serial_barcodes,serial_number',
            'serial_quantity' => 'array',
            'serial_quantity.*' => 'integer'
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
            if(isset($request['serial_number']) and isset($request['serial_quantity'])) {
                if(count($request['serial_number']) != count($request['serial_quantity'])) {
                    return response()-json([
                        'code' => 400,
                        'status' => false,
                        'message' => 'serial_number array and serial_quantity array do not match in size'
                    ]);
                }
                // IN UPDATE, SERIALS ARE OVERWRITTEN. ASSUMING CLIENT MACHINE ALREADY HAS CURRENT SERIALS,
                // FIRST WE "CLEAR OUT" EXISTING ITEMS BY CALLING clearEventItems. THEN ADD THEM BACK WITH NEW ITEMS BY CALLING addEventItems.
                $this->clearEventItems($event['id']);
                $data = $this->addEventItems($request, $event['id']);
                if(!$data['status']) return response()->json($data, 400);
            }
            $event->update($request->all());
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Event Updated successfully'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    // DOESN'T ACTUALLY DELETE IN THIS IMPLEMENTATION
    public function destroy(Event $event) {
        // Check for if event already ended
        if($event->has_ended) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => 'Event has already ended'
            ], 400);
        }
        // First "clear" all the event items.
        $this->clearEventItems($event['id'], $fromDestroy=true);
        // Then either delete event, or update the event->has_ended flag depending on implementation.
        // $event->delete();
        $event->update(['has_ended' => true]);
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Event Deleted successfully',
        ]);
    }

    // APP API TO CLEAR ITEMS FROM event_item_list:
    public function returnFromEvent(Request $request, Event $event) {
        // Check for if event already ended
        if($event->has_ended) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => 'Event has already ended'
            ], 400);
        }
        // "clear/return" the event items received.
        $data = $this->returnItems($request, $event['id']);
        if(!$data['status']) return response()->json($data, 400);
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Item(s) returned from Event successfully'
        ]);
    }

    // HELPER FUNCTIONS:

    // Function for store and update functions
    protected function addEventItems(Request $request, $event_id) {
        foreach(array_combine($request['serial_number'], $request['serial_quantity']) as $serial => $quantity) {
            $itemSerialBarcode = ItemSerialBarcode::where('serial_number', $serial)->first();
            // Following condition should not occur if front-end never has non-available serials
            if(!$itemSerialBarcode->is_available) {
                return [
                    'code' => 400,
                    'status' => false,
                    'message' => "Serial number: $serial is already assigned to an event"
                ];
            } else {
                // SHOULD BE IN A DB TRANSACTION (later)
                // Update available_quantity in items and itemserialbarcodes and set is_available to false in itemserialbarcode
                // REDUNDENT CONDITION? check back after flow diagram is complete, should never execute?
                $item = Item::find($itemSerialBarcode->item_id);
                $newItemQuant = $item->available_quantity - $quantity;
                $newItemSerialQuant = $itemSerialBarcode->available_quantity - $quantity;
                // TO HANDLE THE -ve $quantity
                if($newItemQuant > $item->total_quantity or $newItemSerialQuant > $itemSerialBarcode->total_quantity) {
                    return [
                        'code' => 400,
                        'status' => false,
                        'message' => "Item: $item->name Serial: $itemSerialBarcode->serial_number not available in sufficient quantity. Available: ($item->available_quantity, $itemSerialBarcode->available_quantity)"
                    ];
                }
                if($newItemQuant >= 0 and $newItemSerialQuant >= 0) {
                    $itemSerialBarcode->update([
                        'available_quantity' => $newItemSerialQuant,
                        'is_available' => ($newItemSerialQuant < 1) ? false : true
                    ]);
                    $item->update([
                        'available_quantity' => $newItemQuant
                    ]);
                } else {
                    return [
                        'code' => 400,
                        'status' => false,
                        'message' => "Item: $item->name Serial: $itemSerialBarcode->serial_number not available in sufficient quantity. Available: ($item->available_quantity, $itemSerialBarcode->available_quantity)"
                    ];
                }
                $eventItem = EventItem::create([
                    'event_id' => $event_id,
                    'item_serial_barcode_id' => $itemSerialBarcode->id,
                    'assigned_quantity' => $quantity
                ]);
            }
        }
        // only status is used from below array, everything else does not matter, atleast right now.
        return [
            'code' => 200,
            'status' => true,
            'message' => 'Event Created/Updated successfully'
        ];
    }

    protected function clearEventItems($event_id, $fromDestroy=false) {
        // Make all the items in the deleting event available first, and copy the list into event_item_history table if called from destroy().
        $eventItems = EventItem::where('event_id', $event_id)->get();
        foreach($eventItems as $eventItem) {
            $itemSerialBarcode = ItemSerialBarcode::find($eventItem['item_serial_barcode_id']);
            $item = Item::find($itemSerialBarcode->item_id);
            if($item->available_quantity < $item->total_quantity and $itemSerialBarcode->available_quantity < $itemSerialBarcode->total_quantity) {
                $itemSerialBarcode->update([
                    'available_quantity' => $itemSerialBarcode->available_quantity + $eventItem->assigned_quantity,
                    'is_available' => true
                ]);
                $item->update([
                    'available_quantity' => $item->available_quantity + $eventItem->assigned_quantity
                ]);
            }
            if($fromDestroy) {
                DB::table('event_items_history')->insert([
                    'event_id' => $eventItem->event_id,
                    'item_serial_barcode_id' => $eventItem->item_serial_barcode_id,
                    'assigned_quantity' => $eventItem->assigned_quantity,
                    'created_at' => Carbon::now()
                ]);
            }
            $eventItem->delete();
        }
        // return [
        //     'code' => 200,
        //     'status' => true,
        //     'message' => 'Event Items cleared successfully',
        // ];
    }

    protected function returnItems(Request $request, $event_id) {
        if(!isset($request['serial_id'])) return ['code' => '400', 'status' => false, 'message' => 'No serial_id sent'];
        foreach($request['serial_id'] as $serial_id) {
            try {
                $eventItem = EventItem::where('item_serial_barcode_id', $serial_id)
                    ->where('event_id', $event_id)
                    ->firstOrFail();
            } catch(ModelNotFoundException $e) {
                return [
                    'code' => 400,
                    'status' => false,
                    'message' => "A given serial_id: $serial_id not assigned to the event: $event_id",
                ];
            }
            $itemSerialBarcode = ItemSerialBarcode::find($serial_id);
            $item = Item::find($itemSerialBarcode->item_id);
            if($item->available_quantity < $item->total_quantity and $itemSerialBarcode->available_quantity < $itemSerialBarcode->total_quantity) {
                $itemSerialBarcode->update([
                    'available_quantity' => $itemSerialBarcode->available_quantity + $eventItem->assigned_quantity,
                    'is_available' => true
                ]);
                $item->update([
                    'available_quantity' => $item->available_quantity + $eventItem->assigned_quantity
                ]);
            }
            DB::table('event_items_history')->insert([
                'event_id' => $eventItem->event_id,
                'item_serial_barcode_id' => $eventItem->item_serial_barcode_id,
                'assigned_quantity' => $eventItem->assigned_quantity,
                'created_at' => Carbon::now()
            ]);
            $eventItem->delete();
        }
        return [
            'code' => 200,
            'status' => true,
            'message' => 'Item(s) returned from Event successfully'
        ];
    }

}