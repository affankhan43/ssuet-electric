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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register','Api\UserController@register');
Route::post('login','Api\UserController@login');
Route::post('upStats','Api\dataController@updateStats');
Route::post('upStatsPower','Api\dataController@updateStatsPower');
Route::post('upStatsUnit','Api\dataController@updateStatsUnits');

Route::group(['middleware' => ['jwt.auth']], function() {
	Route::post('getStats','Api\dataController@Stats');
	Route::post('getWallet','Api\dataController@fetchWallet');
	Route::post('updateRate','Api\dataController@updateRate');
	Route::post('getRate','Api\dataController@getRates');
	Route::get('test', function(){
		return response()->json(['foo'=>'bar']);
	});
});