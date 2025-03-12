<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;

// API Routes for categories
Route::apiResource('categories', CategoryController::class);