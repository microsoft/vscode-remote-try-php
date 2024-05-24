<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('un-register-subjects/{id}',  [\App\Http\Controllers\SubjectController::class,'unSubject']);
Route::get('point/{id}',  [\App\Http\Controllers\SubjectController::class,'seenPoint']);
Route::get('point-subjects/{id}',  [\App\Http\Controllers\SubjectController::class,'pointSubjects']);


