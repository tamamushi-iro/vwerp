<?php

namespace App\Http\Controllers;

use App\Tag;
use Illuminate\Http\Request;
use Validator;

class TagController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if($request['show_id']) {
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => [
                    'class' => Tag::select('id', 'tag_name')->where('tag_type', 'class')->get(),
                    'category' => Tag::select('id', 'tag_name')->where('tag_type', 'category')->get(),
                    'type' => Tag::select('id', 'tag_name')->where('tag_type', 'type')->get()
                ]
            ]);
        }
        $tagCollection = Tag::all();
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => [
                'class' => $tagCollection->where('tag_type', 'class')->pluck('tag_name'),
                'category' => $tagCollection->where('tag_type', 'category')->pluck('tag_name'),
                'type' => $tagCollection->where('tag_type', 'type')->pluck('tag_name')
            ]
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
            'tag_name' => 'required|string|unique:tags',
            'tag_type' => 'required|string'
        ], [
            'tag_name.unique' => 'Tag already exists',
        ]);

        if($validator->fails()) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        } else {
            $tag = Tag::create($validator->validated());
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Tag stored successfully',
                'data' => $tag
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag) {
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $tag
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tag $tag) {
        $tag->update($request->all());
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Tag updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag) {
        try {
            $tag->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'debug' => $e->getMessage(),
                'message' => 'Tag cannot be deleted. It is probably in use.'
            ]);
        }
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Tag deleted successfully'
        ]);
    }
}
