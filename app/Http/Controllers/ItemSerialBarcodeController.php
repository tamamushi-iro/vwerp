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
    // public function store(Request $request)
    // {
    //     //
    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\ItemSerialBarcode  $itemSerialBarcode
     * @return \Illuminate\Http\Response
     */
    public function show($serialNumber)
    {
        $itemSerialBarcode = ItemSerialBarcode::where('serial_number', $serialNumber)->first();
        // $itemSerialBarcode['item_name'] = $itemSerialBarcode->item->name;
        return response()->json([
            'id' => $itemSerialBarcode->id,
            'serial_number' => $itemSerialBarcode->serial_number,
            'item_id' => $itemSerialBarcode->item_id,
            'item_name' => $itemSerialBarcode->item->name,
            'qrcod_path' => $itemSerialBarcode->qrcode_path
        ]);
        // return $itemSerialBarcode;
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
