<?php

namespace App\Http\Controllers;

use App\Event;
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
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => Event::all()
        ]);
        // return Event::all();
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
            'priority' => 'string'
            // 'invoice_number' => 'required|unique:events'
        ]);

        if($validator->fails()) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        } else {
            $event = Event::create($validator->validated());
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
    public function show(Event $event)
    {
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $event
        ]);
        // return $event;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event) {
        $event->update($request->all());
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
    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Event Deleted successfully'
        ]);
    }
}
