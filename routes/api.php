<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\AllocationsController;
use App\Http\Controllers\EnrollmentsController;
use App\Http\Controllers\CompletionsController;

// Public Routes
Route::post('login', [AuthController::class, 'login']);
Route::post('signup', [AuthController::class, 'signup']);


//Protected Routers
Route::middleware('auth:api')->group(function () {

    Route::get('/logout', [AuthController::class, 'logout']);

    // Users Methods
    Route::get("/users/count/", [UserController::class, 'count']);
    Route::resource('/users', UserController::class);

    Route::resource('/departments', DepartmentController::class);
    Route::resource('/categories', CategoryController::class);

    // Company Courses Methods
    Route::get("/courses/count/", [CourseController::class, 'count']);
    Route::resource('/courses', CourseController::class);

    // Lectures
    Route::resource('/lectures', LectureController::class);

    // Allocations
    Route::post("/allocations/", [AllocationsController::class, 'store']);
    Route::delete("/allocations/", [AllocationsController::class, 'destroy']);
    Route::get("/allocations/{department_id}/", [AllocationsController::class, 'allocated']);
    Route::get("/allocations/{department_id}/unallocated", [AllocationsController::class, 'unallocated']);

    // ------------------------------------- TODO LIST: -----------------------------------
    // Modify token lifetime to 30 days

    // ********* Mobile users Methods:

    // Current user courses:
    Route::get("/usercourses/", [CourseController::class, 'userCourses']);

    // Course Enrollments
    Route::post("/enrollments/", [EnrollmentsController::class, 'store']);
    Route::delete("/enrollments/{id}", [EnrollmentsController::class, 'destroy']);

    // Lecture Completion
    Route::post("/completions", [CompletionsController::class, 'store']);
    Route::delete("/completions/{id}", [CompletionsController::class, 'destroy']);
});
