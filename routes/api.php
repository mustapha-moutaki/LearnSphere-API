<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\api\V2\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\api\V2\RolesController;
use App\Http\Controllers\Api\SubcategoryController;
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

// logoout and  profile
Route::middleware(['auth:sanctum'])->prefix('V2')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
});

//generate refresh token
Route::post('/refresh', [AuthController::class, 'refreshToken']);

//enrollemnts
Route::post('/enrollments', [EnrollmentController::class, 'enroll']);
Route::get('/enrollments', [EnrollmentController::class, 'index']);
Route::get('/enrollments/user/{id}', [EnrollmentController::class, 'userEnrollments']);
Route::delete('/enrollments/{id}', [EnrollmentController::class, 'unenroll']);

//edit profile and update the image
Route::post('/update-profile', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');

// statistics 
Route::get('/statistics', [StatisticsController::class, 'getStatistics']);

// show roles
Route::get('/roles', [RolesController::class, 'index']);

/* 

---------------------------------------------------------------------
uisng middelarwe ->check admin role to access to logout and profile  \
---------------------------------------------------------------------

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('V2')->group(function () {
    return response()->json(['message' => 'Welcome Admin']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
}); */