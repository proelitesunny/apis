<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::get('/home', 'HomeController@index');
Route::get('/', 'HomeController@index');

Route::group(["prefix" => "admin", "middleware" => ["auth"], "namespace" => "Admin"], function () {
    
    Route::get('docs/api/aggregator/v1', 'DocumentationController@aggregatorDocsV1')
            ->name('aggregator-docs-v1');
});
