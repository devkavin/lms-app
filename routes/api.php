<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// public
Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login', [AuthController::class, 'login']);

// protected
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/v1/user', [AuthController::class, 'user']);
    Route::post('/v1/logout', [AuthController::class, 'logout']);

    // Admin

    Route::get('/v1/admin/users', [AdminController::class, 'viewAllUsers']);
    Route::get('/v1/admin/instructors', [AdminController::class, 'viewAllInstructors']);
    Route::get('/v1/admin/students', [AdminController::class, 'viewAllStudents']);

    // Courses
    Route::get('/courses', [CourseController::class, 'index']);
    Route::post('/courses', [CourseController::class, 'store']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    Route::put('/courses/{id}', [CourseController::class, 'update']); // update course
    Route::delete('/courses/{id}', [CourseController::class, 'destroy']); // delete course

    Route::put('/courses/{id}/enroll', [CourseController::class, 'enroll']); // update course
    Route::put('/courses/{id}/unenroll', [CourseController::class, 'unenroll']); // update course
    Route::get('/courses/{id}/is_enrolled', [CourseController::class, 'isEnrolled']); // update course



});
