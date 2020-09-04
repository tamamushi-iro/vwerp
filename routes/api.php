<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'api', 'prefix' => 'admin'], function ($router) {
    Route::post('register', 'AdminController@register');
    // Route::post('register', 'AdminController@registrationsClosed');
    Route::post('login', 'AdminController@login');
    Route::get('logout', 'AdminController@logout');
    Route::get('refresh', 'AdminController@refresh');
    Route::get('profile', 'AdminController@profile');
});

Route::group(['middleware' => 'api', 'prefix' => 'user'], function ($router) {
    Route::post('register', 'UserController@register');
    // Route::post('register', 'UserController@registrationsClosed');
    Route::post('login', 'UserController@login');
    Route::get('logout', 'UserController@logout');
    Route::get('refresh', 'UserController@refresh');
    Route::get('profile', 'UserController@profile');
});

Route::group(['middleware' => 'api'], function ($router) {
    Route::apiResource('items', 'ItemController')->except(['update']);
    Route::post('items/update/{item}', 'ItemController@update');
    Route::apiResource('serials', 'ItemSerialBarcodeController')->only(['show', 'destroy']);
    Route::post('serials/update/{serial}', 'ItemSerialBarcodeController@update');
    Route::apiResource('items/{item}/serials', 'ItemSerialBarcodeController')->only(['index']);
});

Route::group(['middleware' => 'api'], function ($router) {
    Route::apiResource('events', 'EventController')->except(['update']);
    Route::post('events/update/{event}', 'EventController@update');
    Route::apiResource('events/{event}/items', 'EventItemController')->only(['index']);
});

Route::group(['middleware' => 'api'], function () {
    Route::apiResource('tags', 'TagController')->except(['update']);
    Route::post('tags/update/{tag}', 'TagController@update');
});

// Route::fallback(function() {
//     return response()->json([
//         'code' => 404,
//         'message' => '404 Requested API Resource Not Found'
//     ], 404);
// });