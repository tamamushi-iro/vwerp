<?php

namespace App\Http\Controllers;

use DNS2D;
use App\LedCabinet;
use App\Item;
use Validator;
use Illuminate\Http\Request;

class LedCabinetController extends Controller {

    public function __construct() {
        $this->middleware('auth:api,admins');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Item $item) {
        if($item->item_type_code == 2) {
            if(isset($request['show']) and $request['show'] == 'in_maintenance') {
                return response()->json([
                    'code' => 200,
                    'status' => true,
                    'data' => $item->ledCabinets->where('in_maintenance', true)
                ]);
            } else {
                return response()->json([
                    'code' => 200,
                    'status' => true,
                    'data' => $item->ledCabinets
                ]);
            }
        } else {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => "Item of item_type_code: $item->item_type_code, expected item_type_code: 2"
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // STORES MULTIPLE RESOURCES IN SINGLE CALL
    public function store(Request $request, Item $item) {
        $validator = Validator::make($request->all(), [
            'total_quantity' => 'required|integer',
            'base_serial' => 'required|string',
        ]);
        if($validator->fails()) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => 'total_quantity should be an Integer and base_serial should be a String.',
                'validator_errors' => $validator->errors()
            ], 400);
        }
        if($item->item_type_code == 2) {
            $total_quantity = $request['total_quantity'];
            $base_serial = $request['base_serial'];
            for($i = 0; $i < $total_quantity; $i++) {
                $serial_number = "$base_serial-" . ($i + 1);
                $qrPath = DNS2D::getBarcodePNGPath(json_encode(['item_id' => $item['id'], 'serial_number' => $serial_number]), 'QRCODE');
                $ledCabinet = LedCabinet::create(['item_id' => $item['id'], 'serial_number' => $serial_number, 'qrcode_path' => $qrPath]);
            }
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => "$total_quantity Led Cabinets Stored successfully."
            ]);
        } else {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => "Item of item_type_code: $item->item_type_code, expected item_type_code: 2"
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LedCabinet  $ledCabinet
     * @return \Illuminate\Http\Response
     */
    public function show(LedCabinet $ledCabinet) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LedCabinet  $ledCabinet
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, LedCabinet $ledCabinet) {
    //     $ledCabinet->update($request->all());
    //     return response()->json([
    //         'code' => 200,
    //         'status' => true,
    //         'message' => 'Led Cabinet Updated successfully'
    //     ]);
    // }
    public function update(Request $request, $ledSerial) {
        $ledCabinet = LedCabinet::where('serial_number', $ledSerial)->firstOrFail();
        $ledCabinet->update($request->all());
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Led Cabinet Updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LedCabinet  $ledCabinet
     * @return \Illuminate\Http\Response
     */
    public function destroy(LedCabinet $ledCabinet) {
        try {
            $ledCabinet->delete();
        } catch(\Illuminate\Database\QueryException $e) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'debug' => $e->getMessage(),
                'message' => "Led Cabinet cannot be deleted. It is probably in use."
            ], 400);
        }
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Led Cabinet Deleted successfully'
        ]);
    }
}
