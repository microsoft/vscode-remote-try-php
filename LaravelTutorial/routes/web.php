<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::post('api/login', [HomeController::class, 'API_Login']);

Route::get('/logout', [HomeController::class, 'logout'])->name('logout');

Route::get('/register', [HomeController::class, 'register'])->name('register');
Route::post('api/register', [HomeController::class, 'API_Register']);