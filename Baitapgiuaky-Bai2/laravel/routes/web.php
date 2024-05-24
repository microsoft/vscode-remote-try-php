<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('HelloWorld');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/signup', function () {
    return view('signup');
});

Route::get('/admin', function () {
    return view('admin');
});