<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubcategoryController;
use App\Http\Controllers\api\V2\AuthController;
use App\Http\Controllers\Api\V2\EnrollmentController;
// API Routes for categories
Route::apiResource('categories', CategoryController::class);
Route::apiResource('subcategories', SubcategoryController::class);
Route::resource('courses', CourseController::class);


Route::apiResource('tags', TagController::class);
// V2
Route::prefix('V2')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum'])->prefix('V2')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
});
Route::post('/refresh', [AuthController::class, 'refreshToken']);

Route::post('/enrollments', [EnrollmentController::class, 'enroll']);
Route::get('/enrollments', [EnrollmentController::class, 'index']);
Route::get('/enrollments/user/{id}', [EnrollmentController::class, 'userEnrollments']);
Route::delete('/enrollments/{id}', [EnrollmentController::class, 'unenroll']);

/* 
uisng middelarwe ->check admin role to access to logout and profile

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('V2')->group(function () {
    return response()->json(['message' => 'Welcome Admin']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
}); */

// ---------for testing ---------
// Route::middleware(['role:admin'])->get('/admin', function () {
//     return response()->json(['message' => 'Welcome Admin']);
// });

// Route::middleware(['role:admin,student'])->get('/dashboard', function () {
//     return response()->json(['message' => 'Welcome Admin or student']);
// });