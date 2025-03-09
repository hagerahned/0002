<?php

use App\Http\Controllers\Admin\Auth\ManagerAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\InstructorController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Instructor\AssignmentController;
use App\Http\Controllers\Instructor\AttendanceController;
use App\Http\Controllers\Instructor\Auth\InstructorAuthController;
use App\Http\Controllers\Student\CommentController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::controller(AuthController::class)->group(function () {

    Route::post('login','login');
    Route::post('logout','logout')->middleware(['auth:sanctum']);

    Route::prefix('manager')->middleware(['auth:sanctum', 'is_manager'])->group(function () {
        Route::post('login', 'login')->withoutMiddleware(['auth:sanctum', 'is_manager']);
        Route::post('logout', 'logout');

        Route::prefix('instructor')->controller(InstructorController::class)->group(function () {
            Route::post('/store', 'store');
            Route::post('/show', 'show');
            Route::post('/update', 'update');
            Route::post('/delete', 'delete');
            Route::post('/restore', 'restore');
        });

        Route::prefix('student')->controller(StudentController::class)->group(function () {
            Route::post('/import', 'import');
            Route::get('/export', 'export')->withoutMiddleware(['auth:sanctum', 'is_manager']);
        });

        Route::prefix('course')->controller(CourseController::class)->group(function () {
            Route::post('/store', 'store');
            Route::post('/show', 'show');
            Route::post('/update', 'update');
            Route::post('/delete', 'delete');
            Route::post('/restore', 'restore');
            Route::get('/getAllEnrollmentStudents', 'getAllEnrollmentStudents');
            Route::post('/acceptStudent', 'acceptStudent');
        });

        Route::prefix('category')->controller(CategoryController::class)->group(function () {
            Route::post('/store', 'store');
            Route::post('/show', 'show');
            Route::post('/update', 'update');
            Route::post('/delete', 'delete');
            Route::post('/restore', 'restore');
        });

        Route::prefix('post')->controller(PostController::class)->group(function () {
            Route::post('/store', 'store');
            Route::post('/show', 'show');
            Route::post('/update', 'update');
            Route::post('/delete', 'delete');
            Route::post('/restore', 'restore');
        });
    });
    Route::prefix('instructor')->middleware(['auth:sanctum', 'is_instructor'])->group(function () {
        Route::post('login', 'login')->withoutMiddleware(['auth:sanctum', 'is_instructor']);
        Route::post('logout', 'logout');

        Route::prefix('attendance')->controller(AttendanceController::class)->group(function () {
            Route::post('/store', 'store');
        });
        Route::prefix('assignment')->controller(AssignmentController::class)->group(function () {
            Route::post('/store', 'store');
        });
    });

    Route::prefix('student')->middleware(['auth:sanctum', 'is_student'])->group(function () {
        Route::post('login', 'login')->withoutMiddleware(['auth:sanctum', 'is_student']);
        Route::post('logout', 'logout');

        Route::prefix('course')->controller(StudentCourseController::class)->group(function () {
            Route::post('enroll', 'enroll');
            Route::get('/getAllcourses', 'getAllcourses');
        });

        Route::prefix('comment')->controller(CommentController::class)->group(function () {
            Route::post('/store', 'store');
            Route::post('/update', 'update');
            Route::post('/delete', 'delete');
        });
    });
});
