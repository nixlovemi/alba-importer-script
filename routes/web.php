<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'App\Http\Controllers\Main@index')->name('site.index');
Route::post('/sendFile', 'App\Http\Controllers\Main@sendFile')->name('site.doSendFile');
Route::get('/columns', 'App\Http\Controllers\Main@columns')->name('site.columns');
Route::post('/map-and-download', 'App\Http\Controllers\Main@mapAndDownloadFile')->name('site.mapAndDownloadFile');
