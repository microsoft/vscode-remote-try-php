<?php


use Illuminate\Support\Facades\Route;
Route::get('/hello', function () {
    return 'Hello, World!';
});


Route::get('/', function () {
    return view('welcome');
});
