<?php

namespace App\Http\Controllers;

use App\Item;
use App\ItemSerialBarcode;
use App\Http\Resources\ItemResource;
use Validator;
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
        return ItemResource::collection(Item::all());
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
            'serial_number' => 'required'
        ], [
            'name.unique' => "Item already exists"
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
                // To-Do: Barcode generate here?
                $itemSerial = ItemSerialBarcode::create(['item_id' => $item['id'], 'serial_number' => $serial]);
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
        return new ItemResource($item);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        //
    }
}
