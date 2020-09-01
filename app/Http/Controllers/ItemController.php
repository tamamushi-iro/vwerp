<?php

namespace App\Http\Controllers;

use DNS2D;
use App\Item;
use App\ItemSerialBarcode;
use App\Http\Resources\ItemResource;
use Validator;
use Throwable;
use Illuminate\Http\Request;

class ItemController extends Controller {
    public function __construct() {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $itemResource = ItemResource::collection(Item::all());
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $itemResource
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
            'name' => 'required|unique:items',
            'total_quantity' => 'required|integer',
            'class' => 'required|integer',
            'category' => 'required|integer',
            'type' => 'required|integer',
            'serial_number' => 'required|array',
            'serial_number.*' => 'required|distinct|string|unique:item_serial_barcodes,serial_number',
            'serial_quantity' => 'required|array',
            'serial_quantity.*' => 'required|integer'
        ], [
            'name.unique' => 'Item already exists',
            'serial_number.*.unique' => 'A serial number you entered already exists'
        ]);

        if($validator->fails()) {
            return response()->json([
                'code' => 400,
                'status' => false,
                // No same person should write the code below.
                'message' => ($validator->errors()->has('serial_number.*')) ? current($validator->errors()->get('serial_number.*'))[0] : $validator->errors(),
                'validator_errors' => $validator->errors()
            ], 400);
        } else {
            $item = Item::create(array_merge($validator->validated(), ['available_quantity' => $request['total_quantity']]));
            foreach(array_combine($request['serial_number'], $request['serial_quantity']) as $serial => $quantity) {
                // qrcode is generated here.
                $qrPath = DNS2D::getBarcodePNGPath(json_encode(['item_id' => $item['id'], 'serial_number' => $serial]), 'QRCODE');
                $itemSerial = ItemSerialBarcode::create(['item_id' => $item['id'], 'serial_number' => $serial, 'total_quantity' => $quantity, 'available_quantity' => $quantity, 'qrcode_path' => $qrPath]);
            }
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Item stored successfully'
            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item) {
        $itemResource = new ItemResource($item);
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $itemResource
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item) {
        $validator = Validator::make($request->all(), [
            'serial_number' => 'array',
            'serial_number.*' => 'distinct|string|unique:item_serial_barcodes,serial_number',
            'serial_quantity' => 'array',
            'serial_quantity.*' => 'integer'
        ], [
            'serial_number.*.unique' => 'A serial number you entered already exists'
        ]);

        if($validator->fails()) {
            return response()->json([
                'code' => 400,
                'status' => false,
                // WEIRDLY PROUD OF WHATS HAPPENING IN THE BELOW CODE
                'message' => ($validator->errors()->has('serial_number.*')) ? current($validator->errors()->get('serial_number.*'))[0] : $validator->errors(),
                'validator_errors' => $validator->errors()
            ], 400);
        } else {
            // ONLY NEW SERIALS ARE ADDED HERE. TO DELETE/CHANGE OLD SERIALS, USE API'S OF ItemSerialBarcodeController
            if(isset($request['serial_number']) and isset($request['serial_quantity'])) {
                if(count($request['serial_number']) != count($request['serial_quantity'])) {
                    return response()-json([
                        'code' => 400,
                        'status' => false,
                        'message' => 'serial_number array and serial_quantity array do not match in size'
                    ], 400);
                }
                foreach(array_combine($request['serial_number'], $request['serial_quantity']) as $serial => $quantity) {
                    // qrcode is generated here.
                    $qrPath = DNS2D::getBarcodePNGPath(json_encode(
                        ['item_id' => $item['id'], 'serial_number' => $serial]
                    ), 'QRCODE');
                    $itemSerial = ItemSerialBarcode::create([
                        'item_id' => $item['id'], 'serial_number' => $serial,
                        'total_quantity' => $quantity, 'available_quantity' => $quantity,
                        'qrcode_path' => $qrPath
                    ]);
                }
            }
            $item->update($request->all());
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Item Updated successfully'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item) {
        $item->delete();
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Item Deleted successfully'
        ]);
    }
}
