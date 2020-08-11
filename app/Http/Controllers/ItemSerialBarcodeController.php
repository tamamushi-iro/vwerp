<?php

namespace App\Http\Controllers;

use App\Item;
use App\ItemSerialBarcode;
// use App\Http\Resources\SerialResource;
use Illuminate\Http\Request;

class ItemSerialBarcodeController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Item $item)
    {
        return $item->item_serial_barcodes;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Item $item)
    {
        // This function acting like update for items
        $validator = Vadidator::make($request->all(), [
            'serial_number' => 'required|array',
            'serial_number' => 'required|distinct|string|unique:item_serial_barcodes,serial_number'
        ], [
            'serial_number.*.unique' => 'Serial number already exists'
        ]);

        if($validator->fails()) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ItemSerialBarcode  $itemSerialBarcode
     * @return \Illuminate\Http\Response
     */
    public function show($serialNumber)
    {
        $itemSerialBarcode = ItemSerialBarcode::where('serial_number', $serialNumber)->first();
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => [
                'id' => $itemSerialBarcode->id,
                'serial_number' => $itemSerialBarcode->serial_number,
                'item_id' => $itemSerialBarcode->item_id,
                'item_name' => $itemSerialBarcode->item->name,
                'qrcode_path' => $itemSerialBarcode->qrcode_path
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ItemSerialBarcode  $itemSerialBarcode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ItemSerialBarcode $itemSerialBarcode)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ItemSerialBarcode  $itemSerialBarcode
     * @return \Illuminate\Http\Response
     */
    public function destroy(ItemSerialBarcode $itemSerialBarcode)
    {
        //
    }
}
