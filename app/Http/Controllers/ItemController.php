<?php

namespace App\Http\Controllers;

use DNS2D;
use App\Item;
use App\ItemSerialBarcode;
use App\Http\Resources\ItemResource;
use Validator;
use Throwable;
use Illuminate\Http\Request;

class ItemController extends Controller
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
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:items',
            'quantity' => 'required|integer',
            'class' => 'required',
            'category' => 'required',
            'type' => 'required',
            'serial_number' => 'required|array',
            'serial_number.*' => 'required|distinct|string|unique:item_serial_barcodes,serial_number'
        ], [
            'name.unique' => 'Item already exists',
            'serial_number.*.unique' => 'Serial number already exists'
        ]);

        if($validator->fails()) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        } else {
            $item = Item::create($validator->validated());
            foreach($request['serial_number'] as $serial) {
                // qrcode is generated here.
                $qrPath = DNS2D::getBarcodePNGPath(json_encode(
                    ['item_id' => $item['id'], 'serial_number' => $serial]
                ), 'QRCODE');
                $itemSerial = ItemSerialBarcode::create(['item_id' => $item['id'], 'serial_number' => $serial, 'qrcode_path' => $qrPath]);
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
    public function show(Item $item)
    {
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
    // SERIAL VADU UPDATE BAKI?
    public function update(Request $request, Item $item)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        } else {
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
    public function destroy(Item $item)
    {
        $product->delete();
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Item Deleted successfully'
        ]);
    }
}
