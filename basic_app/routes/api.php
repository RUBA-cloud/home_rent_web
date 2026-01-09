<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BookingOrderController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CompanyBranchController;
use App\Http\Controllers\Api\CompanyInfoController;
use App\Http\Controllers\Api\FaviorateController; // Consider renaming "FaviorateController" for clarity
use App\Http\Controllers\Api\FilterApiController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\HomeRentController;

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController; // ✅ fixed typo
use App\Http\Controllers\Api\PhoneAuthController;
// Middleware
use App\Http\Middleware\JWTAuthMiddleware;

// --- Public Auth Routes ---
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthApiController::class, 'register']);
    Route::post('login', [AuthApiController::class, 'login']);
    Route::post('forgot-password', [AuthApiController::class, 'forgotPassword']);
    Route::post('resend-forgot-password', [AuthApiController::class, 'resendForgotPassword']);
    Route::post('resend-verify_email', [AuthApiController::class, 'resendVerificationEmail']);
    Route::post('phone', [PhoneAuthController::class, 'sendOtp']);
    Route::post('sms', [PhoneAuthController::class, 'sendsms']);
});

Route::get('company-info', [CompanyInfoController::class, 'index']);

// --- Protected Routes ---
Route::middleware([JWTAuthMiddleware::class])->group(function () {

    // Auth actions
    Route::post('logout', [AuthApiController::class, 'logout']);
    Route::post('refresh', [AuthApiController::class, 'refresh']);
    Route::post('user/profile', [AuthApiController::class, 'updateProfile']);
    Route::post('update-settings', [AuthApiController::class, 'updateSettings']);
    Route::post('change-password', [AuthApiController::class, 'changePassword']);

    // Company Info
    Route::get('company-info/first/stream', [CompanyInfoController::class, 'streamFirst']);
    Route::get('company-info/first/long-poll', [CompanyInfoController::class, 'longPollFirst']);
    Route::get('company-branch', [CompanyBranchController::class, 'index']);

    // Authenticated user
    Route::get('user', function (Request $request) {
        return response()->json($request->user());
    });

    // Categories
    Route::get('categories', [HomeController::class, 'index']);
    Route::get('category/{id}', [CategoryController::class, 'show']);
    Route::post('category_search', [CategoryController::class, 'search']);

    // Filter
    Route::get('filter', [FilterApiController::class, 'index']);
    Route::post('filter', [FilterApiController::class, 'filter']);

    // Favorites (was FaviorateController — check spelling)
    Route::get('faviorate_list', [FaviorateController::class, 'index']);
    Route::post('add-faviorate', [FaviorateController::class, 'addFaviorate']);
    Route::get('remove-faviorate/{id}', [FaviorateController::class, 'removeFaviorate']);
    Route::get('remove-product-faviorate/{id}', [FaviorateController::class, 'removeProductFaviorate']);
    Route::get('clear_all_faviorate', [FaviorateController::class, 'clearAllFaviorate']);
    Route::post('search_faviorate', [FaviorateController::class, 'search']);

    // Cart
    Route::get('cart', [CartController::class, 'index']);
    Route::post('add-to-cart', [CartController::class, 'addToCart']);
    Route::post('update-cart-quantity', [CartController::class, 'updateQuantity']);
    Route::post('remove-from-cart', [CartController::class, 'removeFromCart']); // ✅ kept only once

    // Orders
    Route::post('make_order', [OrderController::class, 'store']);
    Route::get('orders', [OrderController::class, 'index']);

    // Home Rent Search
    Route::post('home_rent/search', [HomeController::class, 'getHome']);

    // Payment
    Route::post('/create-setup-intent', [PaymentController::class, 'createPaymentIntent']);
    Route::post('/subscribe', [PaymentController::class, 'subscribe']);

    // Bookings
    Route::get('/bookings_index', [BookingOrderController::class, 'index']);
    Route::post('/bookings', [BookingOrderController::class, 'store']);
    Route::post('/update_booking', [BookingOrderController::class, 'update']);
    Route::post('/remove_booking/{id}', [BookingOrderController::class, 'destroy']);

 Route::get('/home_rent_categories',[HomeRentController::class,'categories']);
 Route::get('/home_rent_features',[HomeRentController::class,'features']);

});

// WebSocket / Broadcasting support
Broadcast::routes();
