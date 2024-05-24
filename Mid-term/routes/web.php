<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\CheckLanguage;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;

Route::middleware([CheckLanguage::class])->group(function () {
    Auth::routes(['register' => false]);
    Route::get('/change-language/{language}', [HomeController::class, 'changeLanguage'])->name('language.change');
    Route::middleware(['auth'])->group(function () {
        Route::group(['prefix' => 'admin','middleware' => 'checkAdmin'], function () {
            Route::get('/', [HomeController::class, 'index'])->name('admin.index');
            Route::resource('faculties', FacultyController::class);
            Route::resource('students', StudentController::class);
            Route::resource('subjects', SubjectController::class);
            Route::get('result',[SubjectController::class,'resultAll'])->name('result_all');
            Route::post('send-mail/{id}',[SubjectController::class,'send'])->name('mail.send');
            Route::get('point/{id}',[SubjectController::class,'point'])->name('point');
            Route::post('point',[StudentController::class,'point'])->name('point.update');
            Route::get('points-update/{id}',[SubjectController::class,'change'])->name('points.change');
            Route::post('points-update',[SubjectController::class,'updatePoints'])->name('points.update');
            Route::post('import',[SubjectController::class,'import'])->name('students.import');
        });
        Route::group(['prefix' => 'student','middleware' => 'checkStudent'], function () {
            Route::get('/', [StudentController::class, 'home'])->name('student.index');
            Route::get('register-subject',[SubjectController::class,'registerSubject'])->name('subject.register');
            Route::post('register',[SubjectController::class,'register'])->name('register');
            Route::get('result',[SubjectController::class,'result'])->name('result');
        });
    });
});
Route::fallback(function () {
    return view('404');
});
