<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1/','middleware'=>'auth:sanctum'], function () {
    Route::get('user/profile', [App\Http\Controllers\Api\AuthController::class, 'profile']);
    Route::patch('user/profile', [App\Http\Controllers\Api\AuthController::class, 'profileUpdate']);
    Route::get('categories',[App\Http\Controllers\Api\CategoryController::class,'get']);
    Route::patch('user/products/{productId}/favourite',[App\Http\Controllers\Api\ProductController::class,'favouriteProduct'])->where('productId', '[0-9]+');
    Route::get('user/products/favourite',[App\Http\Controllers\Api\ProductController::class,'getFavouriteProduct']);
    Route::get('products/featured',[App\Http\Controllers\Api\ProductController::class,'getFeatured']);
    Route::get('products/filtered',[App\Http\Controllers\Api\ProductController::class,'filterProduct']);
    Route::get('user/products',[App\Http\Controllers\Api\ProductController::class,'yourProduct']);
    Route::post('user/products',[App\Http\Controllers\Api\ProductController::class,'store']);
    Route::delete('user/products/{productId}',[App\Http\Controllers\Api\ProductController::class,'destroy']);
    Route::post('user/products/{productId}',[App\Http\Controllers\Api\ProductController::class,'update']);
    Route::get('productDetail/{productId}',[App\Http\Controllers\Api\ProductController::class,'productDetail'])->where('productId', '[0-9]+');

   //cart end point routes
    Route::get('user/carts',[App\Http\Controllers\Api\CartController::class,'getCart']);
    Route::post('user/carts',[App\Http\Controllers\Api\CartController::class,'store']);
    Route::patch('user/carts',[App\Http\Controllers\Api\CartController::class,'destroy']);
     //ORDER ROUTES
    Route::post('user/orders',[App\Http\Controllers\Api\OrderController::class,'store']);
    Route::put('user/orders/{orderId}',[App\Http\Controllers\Api\OrderController::class,'update']);
    Route::get('user/orders',[App\Http\Controllers\Api\OrderController::class,'getOrders']);
    Route::get('user/orders/{orderId}',[App\Http\Controllers\Api\OrderController::class,'orderDetail'])->where('orderId', '[0-9]+');
    /*=============================== chat User Approved======================= */
    Route::post('user/approval',[App\Http\Controllers\Api\SellerDocumentController::class,'sellerApproved']);
    Route::get('user/earnings',[App\Http\Controllers\Api\SellerDocumentController::class,'getEarning']);

    /*=============================== chat endpoint======================= */
    Route::get('user/messages',[App\Http\Controllers\Api\ChatController::class,'index']);
    Route::get('user/relaunch-data',[App\Http\Controllers\Api\ChatController::class,'userRelaunchData']);
    Route::post('user/notifications/message',[App\Http\Controllers\Api\ChatController::class,'sendMessageNotification']);

    /*=============================== Credit cart endpoint======================= */
    Route::post('user/cards/client-token',[App\Http\Controllers\Api\CardController::class, 'generateClientToken']);
    Route::post('user/cards/test-card',[App\Http\Controllers\Api\CardController::class, 'testCard']);
    Route::get('user/cards',[App\Http\Controllers\Api\CardController::class, 'getCards']);
    Route::delete('user/cards/{cardId}',[App\Http\Controllers\Api\CardController::class, 'deleteCard'])->where('cardId', '[0-9]+');
    Route::post('user/cards',[App\Http\Controllers\Api\CardController::class, 'store']);

    /*=============================== User block endpoint======================= */
    Route::patch('user/user-block', [App\Http\Controllers\Api\AuthController::class, 'userBlock']);
    Route::post('user/password/update', [App\Http\Controllers\Api\ResetPasswordController::class, 'passwordChange']);

});
Route::get('v1/users/dashboard', [App\Http\Controllers\Api\DashboardController::class, 'userDashboard']);

Route::group(['prefix' => 'v1/user/'], function () {
    /*                   Auth routes               */
    Route::post('signup', [App\Http\Controllers\Api\AuthController::class, 'signUp']);
    Route::post('social', [App\Http\Controllers\Api\AuthController::class, 'socialSignin']);
    Route::post('signin', [App\Http\Controllers\Api\AuthController::class, 'signIn']);
    Route::post('otp/send', [App\Http\Controllers\Api\OtpController::class, 'sendOtpCode']);
    Route::post('otp/validate', [App\Http\Controllers\Api\OtpController::class, 'validateOTP']);
    Route::put('reset/password', [App\Http\Controllers\Api\ResetPasswordController::class, 'resetPassword']);
    Route::delete('logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');
});


