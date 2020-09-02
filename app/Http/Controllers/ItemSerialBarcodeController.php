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
    public function index(Item $item) {
        return $item->item_serial_barcodes;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request, Item $item) {
        //
    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\ItemSerialBarcode  $itemSerialBarcode
     * @return \Illuminate\Http\Response
     */
    public function show($serialNumber) {
        $itemSerialBarcode = ItemSerialBarcode::where('serial_number', $serialNumber)->first();
        return response()->json([
            'code' => 200,
            'status' => true,
            // If a Relation in a Model is not accessed after being declared, it is not yet set in the Model's Collection instance.
            // But, if the relation is accessed, then that relation is stored in the collection instance, and is accessable. Like in the following example.
            // 'data' => array_merge([ 'item_name' => $itemSerialBarcode->item->name ], $itemSerialBarcode->toArray())
            'data' => array_merge($itemSerialBarcode->toArray(), ['item_name' => $itemSerialBarcode->item->name])
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ItemSerialBarcode  $itemSerialBarcode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ItemSerialBarcode $serial) {
        // $itemSerialBarcode = ItemSerialBarcode::where('serial_number', $serialNumber)->first();
        // TO BE REMOVED:
        $serial->update($request->all());
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Serial Updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ItemSerialBarcode  $itemSerialBarcode
     * @return \Illuminate\Http\Response
     */
    public function destroy(ItemSerialBarcode $serial) {
        try {
            $serial->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'debug' => $e->getMessage(),
                'message' => 'Serial cannot be deleted. It is probably in use.'
            ], 400);
        }
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Serial Deleted successfully'
        ]);
    }
}
