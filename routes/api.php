<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\AllocationsController;

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

    // Courses Methods
    Route::get("/courses/count/", [CourseController::class, 'count']);
    Route::resource('/courses', CourseController::class);

    Route::resource('/lectures', LectureController::class);

    // Allocations
    Route::get("/allocations/{department_id}/", [AllocationsController::class, 'allocated']);
    Route::get("/allocations/{department_id}/unallocated", [AllocationsController::class, 'unallocated']);
    Route::post("/allocations/", [AllocationsController::class, 'store']);
    Route::delete("/allocations/", [AllocationsController::class, 'destroy']);
});
