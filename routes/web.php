<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'App\Http\Controllers\Main@index')->name('site.index');
