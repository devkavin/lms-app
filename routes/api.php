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
Route::post('/v1/logout', [AuthController::class, 'logout']);

// admin
Route::group(['middleware' => ['auth:sanctum', 'role:admin']], function () {
    Route::get('/v1/admin/users', [AdminController::class, 'viewAllUsers']);
    Route::get('/v1/admin/instructors', [AdminController::class, 'viewAllInstructors']);
    Route::get('/v1/admin/admins', [AdminController::class, 'viewAllAdmins']);
    Route::delete('/v1/admin/user', [AdminController::class, 'deleteUser']);
    Route::delete('/v1/admin/bulk_delete_users', [AdminController::class, 'bulkDeleteUsers']);
});

// protected
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/v1/user', [AuthController::class, 'user']);

    // Courses
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);

    // Instructor
    Route::post('/instructors/create-course', [CourseController::class, 'store']); // create course
    Route::put('/instructors/update-course/{id}', [CourseController::class, 'update']); // update course
    Route::delete('/instructors/delete-course/{id}', [CourseController::class, 'destroy']); // delete course
    Route::post('/instructors/my-courses', [CourseController::class, 'myCreatedCourses']); // get my created courses

    // Student
    Route::get('/students/my-courses', [CourseController::class, 'myEnrolledCourses']); // get my enrolled courses
    Route::put('/students/courses/{id}/enroll', [CourseController::class, 'enroll']); // student enroll course
    Route::put('/students/courses/{id}/unenroll', [CourseController::class, 'unenroll']); // student unenroll course
    Route::get('/students/courses/{id}/is_enrolled', [CourseController::class, 'isEnrolled']); // check if student is enrolled in a course

});
