<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\SubcategoryController;
use App\Http\Controllers\Api\TagController;

// API Routes for categories
Route::apiResource('categories', CategoryController::class);
Route::apiResource('subcategories', SubcategoryController::class);
Route::resource('courses', CourseController::class);


Route::apiResource('tags', TagController::class);

