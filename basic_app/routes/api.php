<?php
// ...existing code...

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CompanyInfoController;
use App\Http\Controllers\Api\CompanyBranchController;
use App\Http\Middleware\JWTAuthMiddleware;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\FilterApiController;
use App\Http\Controllers\Api\FaviorateController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthApiController::class, 'register']);
    Route::post('login', [AuthApiController::class, 'login']);
    Route::post('forgot-password', [AuthApiController::class, 'forgotPassword']);
    Route::post('resend-forgot-password', [AuthApiController::class, 'resendForgotPassword']);
    Route::post('resend-verify_email', [AuthApiController::class, 'resendVerificationEmail']);
});
    Route::get('company-info', [CompanyInfoController::class, 'index']);

Route::middleware([JWTAuthMiddleware::class])->group(function () {

    Route::post('logout',
    [AuthApiController::class, 'logout']);
      Route::post('refresh', [AuthApiController::class, 'refresh'])
;
    Route::post('user/profile', [AuthApiController::class, 'updateProfile']);
    Route::post('update-settings', [AuthApiController::class, 'updateSettings']);
    Route::post('change-password', [AuthApiController::class, 'changePassword']);
    Route::get('company-info/first/stream', [CoxmpanyInfoController::class, 'streamFirst']);
    Route::get('company-info/first/long-poll', [CompanyInfoController::class, 'longPollFirst']);
    Route::get('company-branch', [CompanyBranchController::class, 'index']);
    Route::get('user', function (Request $request) {
        return response()->json($request->user());
    });
    Route::get('categories',[HomeController::class,'index']);
    Route::get('category/{id}',[CategoryController::class,'show']);
  Route::post('category_search', [CategoryController::class, 'search']);

    Route::get('filter',[FilterApiController::class,'index']);
    Route::post('filter',[FilterApiController::class,'filter']);
    Route::get('faviorate_list', [FaviorateController::class, 'index']);
    Route::post('add-faviorate', [FaviorateController::class, 'addFaviorate']);
    Route::get('remove-faviorate/{id}', [FaviorateController::class, 'removeFaviorate']);
    Route::get('remove-product-faviorate/{id}', [FaviorateController::class, 'removeProductFaviorate']);

    Route::get('clear_all_faviorate', [FaviorateController::class, 'clearAllFaviorate']);
    Route::post('search_faviorate', [FaviorateController::class, 'search']);
    Route::get('cart', [CartController::class, 'index']);
    Route::post('add-to-cart', [CartController::class, 'addToCart']);
    Route::post('update-cart-quantity', [CartController::class, 'updateQuantity']);
    Route::post('remove-from-cart', [CartController::class, 'removeFromCart']);
    Route::post('remove-from-cart', [CartController::class, 'removeFromCart']);

    Route::post('make_order',[OrderController::class,'store']);
    Route::get('orders',[OrderController::class,'index']);


});
 Broadcast::routes();

// ...existing code...
