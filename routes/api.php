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

Route::group(['middleware' => 'api', 'prefix' => 'user'], function ($router) {
    Route::post('register', 'UserController@register');
    // Route::post('/register', 'UserController@registrationsClosed');
    Route::post('login', 'UserController@login');
    Route::post('logout', 'UserController@logout');
    Route::post('refresh', 'UserController@refresh');
    Route::get('profile', 'UserController@profile');
});

Route::group(['middleware' => 'api'], function ($router) {
    Route::apiResource('items', 'ItemController');
    Route::apiResource('items/{item}/serials', 'ItemSerialBarcodeController')->except([
        'store', 'show'
    ]);
    Route::apiResource('serials', 'ItemSerialBarcodeController')->only([
        'show'
    ]);
});

// Route::fallback(function() {
//     return response()->json([
//         'code' => 404,
//         'message' => '404 Requested API Resource Not Found'
//     ], 404);
// });