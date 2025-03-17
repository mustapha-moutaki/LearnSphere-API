<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubcategoryController;
use App\Http\Controllers\api\V2\AuthController;

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
