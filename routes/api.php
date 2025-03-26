<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\Api\V2\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\V2\RolesController;

use App\Http\Controllers\Api\V3\BadgeController;
use App\Http\Controllers\Api\V3\StripeController;
use App\Http\Controllers\Api\SubcategoryController;
use App\Http\Controllers\Api\V2\EnrollmentController;
use App\Http\Controllers\Api\V3\CourseSearchController;
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
    Route::post('/profile', [AuthController::class, 'updateProfile']);
});

//generate refresh token
Route::post('/refresh', [AuthController::class, 'refreshToken']);

//enrollemnts
Route::post('/enrollments', [EnrollmentController::class, 'enroll']);
Route::get('/enrollments', [EnrollmentController::class, 'index']);
Route::get('/enrollments/user/{id}', [EnrollmentController::class, 'userEnrollments']);
Route::delete('/enrollments/{id}', [EnrollmentController::class, 'unenroll']);

//edit profile and update the image
// ->middleware('auth:sanctum');

// statistics 
Route::get('/statistics', [StatisticsController::class, 'getStatistics']);

// show roles
Route::get('/roles', [RolesController::class, 'index']);

//search for a course by description or title
Route::prefix('V3')->group(function () {
    Route::get('/courses/search', [CourseSearchController::class, 'search']);
});


Route::middleware(['auth:sanctum'])->prefix('V3/payments')->group(function () {
    Route::get('/checkout', [StripeController::class, 'checkout']);
    Route::post('/confirm', [App\Http\Controllers\Api\V3\StripeController::class, 'confirmPayment']);
    Route::get('/history', [App\Http\Controllers\Api\V3\StripeController::class, 'paymentHistory']);
    Route::get('/status/{id}', [App\Http\Controllers\Api\V3\StripeController::class, 'paymentStatus']);
});

    Route::get('/payment/success/{enrollment_id}', [PaymentController::class, 'paymentSuccess'])
        ->name('payment.success');
    Route::get('/payment/cancel/{enrollment_id}', [PaymentController::class, 'paymentCancel'])
        ->name('payment.cancel');



Route::prefix('V3')->group(function () {
    // Student badges routes
    Route::get('/students/{id}/badges', [BadgeController::class, 'studentBadges'])
        ->middleware(['auth:sanctum']);

    // Get all badges
    Route::get('/badges', [BadgeController::class, 'index']);

    // Admin badge management routes
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('/badges', [BadgeController::class, 'create']);
        Route::put('/badges/{id}', [BadgeController::class, 'update']);
        Route::delete('/badges/{id}', [BadgeController::class, 'delete']);
    });
});
/* 

---------------------------------------------------------------------
uisng middelarwe ->check admin role to access to logout and profile  \
---------------------------------------------------------------------

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('V2')->group(function () {
    return response()->json(['message' => 'Welcome Admin']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
}); */