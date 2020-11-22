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
    Route::get('index', 'UserController@index');
    Route::delete('delete/{user}', 'UserController@destroy');
});

Route::group(['middleware' => 'api', 'prefix' => 'whuser'], function ($router) {
    Route::post('register', 'WarehouseUserController@register');
    // Route::post('register', 'WarehouseUserController@registrationsClosed');
    Route::post('login', 'WarehouseUserController@login');
    Route::get('logout', 'WarehouseUserController@logout');
    Route::get('refresh', 'WarehouseUserController@refresh');
    Route::get('profile', 'WarehouseUserController@profile');
    Route::get('index', 'WarehouseUserController@index');
    Route::delete('delete/{whuser}', 'WarehouseUserController@destroy');
});

Route::group(['middleware' => 'api'], function ($router) {
    Route::apiResource('items', 'ItemController')->except(['update']);
    Route::post('items/update/{item}', 'ItemController@update');
    Route::apiResource('serials', 'ItemSerialBarcodeController')->only(['show', 'destroy']);
    Route::post('serials/update/{serial}', 'ItemSerialBarcodeController@update');
    Route::apiResource('items/{item}/serials', 'ItemSerialBarcodeController')->only(['index']);
    Route::apiResource('ledCabinets', 'LedCabinetController')->only(['destroy']);
    Route::post('ledCabinets/update/{ledSerial}', 'LedCabinetController@update');
    Route::apiResource('items/{item}/ledCabinets', 'LedCabinetController')->only(['index', 'store']);
});

Route::group(['middleware' => 'api'], function ($router) {
    Route::get('eventsInRange', 'EventController@indexRange');
    Route::get('eventsNotFinal', 'EventController@indexNotFinal');
    Route::apiResource('events', 'EventController')->except(['update', 'indexRange']);
    Route::post('events/update/{event}', 'EventController@update');
    Route::apiResource('events/{event}/items', 'EventItemController')->only(['index']);
    Route::post('events/returnFromEvent/{event}', 'EventController@returnFromEvent');
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