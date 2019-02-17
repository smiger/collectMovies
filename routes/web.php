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

Route::get('/', 'MovieController@searchCollect');

Route::any('search','MovieController@search');

Route::any('play', 'MovieController@play');

Route::any('movie', 'MovieController@searchCollectMore');

Route::any('tv', 'MovieController@searchCollectTv');

Route::any('zongyi', 'MovieController@searchCollectZongyi');

Route::any('dongman', 'MovieController@searchCollectDongman');

Route::any('meiju', 'MovieController@searchCollectMeiju');

Route::get('feedback',function(){
    return view('feedback');
});


Route::post('feedback','MovieController@feedback');

//------采集 begin-------
Route::any('fenlei','MovieController@fenlei');
Route::any('collect','MovieController@collect');
Route::any('searchCollect','MovieController@searchCollect');
Route::any('searchCollectMore','MovieController@searchCollectMore');
Route::any('searchCollectResult','MovieController@searchCollectResult');

Route::any('searchCollectPlay', 'MovieController@searchCollectPlay');
Route::post('collectDownload','MovieController@collectDownload');
Route::post('bind','MovieController@bind');
//------采集 end---------