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

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/','EnterController@index');

Route::post('/addToCart','EnterController@addToCart');

Route::get('/showCategorys/{id}/{page}','EnterController@showCategorys');

Route::get('/showGoods/{id}','EnterController@showGoods');

Route::post('/rmFromCart','EnterController@rmFromCart');

Route::get('/checkout','EnterController@checkout');

Route::post('/getGoodsNum','EnterController@getGoodsNum');

Route::post('/changeGoodsNum','EnterController@changeGoodsNum');

Route::get('/fillData','EnterController@fillData');

Route::post('/areaChange','EnterController@areaChange');

Route::any('/storeMap/{device}/{type}','EnterController@storeMap');

Route::post('/shipChange','EnterController@shipChange');

Route::any('/done','EnterController@done');

// 萬用測試
Route::post('/test','EnterController@test');