<?php

use Illuminate\Http\Request;

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

Route::group([
    'namespace' => 'Api',
    'middleware' => ['run-time-config'],
    'as' => 'api.',
        ], function() {

    /** Aggregator related routes **/
    Route::group(['namespace' => 'Aggregator', 'prefix' => 'aggregator'], function() {
        Route::group(['namespace' => 'V1', 'prefix' => 'v1'], function() {
            require (__DIR__ . '/aggregator_api_routes.php');
        });
    });
    
});