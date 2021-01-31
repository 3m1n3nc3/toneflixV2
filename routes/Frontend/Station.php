<?php
/**
 * Created by NiNaCoder.
 * Date: 2019-08-01
 * Time: 20:35
 */
Route::get('station/{id}/{slug}', 'StationController@index')->name('station');
Route::post('station/report', 'StationController@report')->name('station.report');
Route::post('station/played', 'StationController@report')->name('station.played');
