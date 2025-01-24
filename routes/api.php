<?php

use App\Http\Controllers\Admin\Auth\ManagerAuthController;
use App\Http\Controllers\Admin\InstructorController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Instructor\Auth\InstructorAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('manager')->middleware(['auth:sanctum','is_manager'])->controller(ManagerAuthController::class)->group(function(){
    Route::post('login','login')->withoutMiddleware(['auth:sanctum','is_manager']);
    Route::post('logout','logout');

    Route::prefix('instructor')->controller(InstructorController::class)->group(function(){
        Route::post('/store','store');
        Route::post('/show','show');
        Route::post('/update','update');
        Route::post('/delete','delete');
        Route::post('/restore','restore');
    });

    Route::prefix('student')->controller(StudentController::class)->group(function(){
        Route::post('/import','import');
        Route::get('/export','export');
    });
});
Route::prefix('instructor')->middleware(['auth:sanctum','is_instructor'])->controller(InstructorAuthController::class)->group(function(){
    Route::post('login','login')->withoutMiddleware(['auth:sanctum','is_instructor']);
    Route::post('logout','logout');
});
