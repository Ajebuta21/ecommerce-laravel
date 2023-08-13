<?php

use App\Http\Controllers\AdController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DeliveryOptionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/create-user', [UserController::class, 'createUser']);
Route::group([

    'middleware' => 'api',

], function ($router) {
    //user
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/me', [AuthController::class, 'me']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::post('/user/role/{id}', [UserController::class, 'changeRole']);
    Route::get('/users/search', [UserController::class, 'search']);
    Route::post('/edit-user/{id}', [UserController::class, 'editUser']);
    Route::post('/edit-address', [UserController::class, 'updateAddress']);
    Route::post('/reset-user-password', [UserController::class, 'resetPassword']);
    Route::delete('/delete-user/{id}', [UserController::class, 'deleteUser']);
    Route::get('/users/count', [UserController::class, 'countUsers']);
    //category
    Route::post('/create-category', [CategoryController::class, 'store']);
    Route::post('/categories/destroy/{id}', [CategoryController::class, 'destroy']);
    Route::post('/categories/update/{id}', [CategoryController::class, 'update']);
    Route::get('/categories/count-last-30-days', [CategoryController::class, 'countCategoriesLast30Days']);
    //brand
    Route::post('/create-brand', [BrandController::class, 'store']);
    Route::post('/brands/destroy/{id}', [BrandController::class, 'destroy']);
    Route::post('/brands/update/{id}', [BrandController::class, 'update']);
    Route::get('/brands/count-last-30-days', [BrandController::class, 'countBrandsLast30Days']);
    //products
    Route::post('/create-product', [ProductController::class, 'store']);
    Route::post('/products/destroy/{id}', [ProductController::class, 'destroy']);
    Route::post('/products/update/{id}', [ProductController::class, 'update']);
    Route::get('/products/count-last-30-days', [ProductController::class, 'countProductsLast30Days']);
    Route::get('/products/top-rated', [ProductController::class, 'getTopRatedProducts']);
    //ratings
    Route::post('/create-ratings', [RatingController::class, 'store']);
    Route::get('/rating/products/{product_id}/user/{user_id}', [RatingController::class, 'showUserRating']);
    //delivery locations
    Route::post('/create-delivery-options', [DeliveryOptionController::class, 'store']);
    Route::post('/delivery-options/update/{id}', [DeliveryOptionController::class, 'update']);
    Route::delete('/delivery-options/destroy/{id}', [DeliveryOptionController::class, 'destroy']);
    //order
    Route::get('/order', [OrderController::class, 'index']);
    Route::post('/create-order', [OrderController::class, 'store']);
    Route::get('/orders/show/{id}', [OrderController::class, 'show']);
    Route::post('/orders/update-status/{id}', [OrderController::class, 'update']);
    Route::get('/orders/search', [OrderController::class, 'search']);
    Route::get('/orders/user/{user_id}', [OrderController::class, 'getOrdersByUserId']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
    Route::get('/orders/count', [OrderController::class, 'count']);
    Route::get('/orders/total-sum', [OrderController::class, 'sum']);
    Route::get('/orders/chart', [OrderController::class, 'monthly']);
    //ads
    Route::post('/create-ad', [AdController::class, 'store']);
    Route::get('/ads', [AdController::class, 'index']);
    Route::post('/delete-ad/{id}', [AdController::class, 'destroy']);
});
//category
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/search', [CategoryController::class, 'search']);
Route::get('/categories/count', [CategoryController::class, 'count']);
Route::get('/categories/show/{id}', [CategoryController::class, 'show']);
//brand
Route::get('/brands', [BrandController::class, 'index']);
Route::get('/brands/search', [BrandController::class, 'search']);
Route::get('/brands/count', [BrandController::class, 'count']);
Route::get('/brands/show/{id}', [BrandController::class, 'show']);
//products
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/products/count', [ProductController::class, 'count']);
Route::get('/products/show/{id}', [ProductController::class, 'show']);
Route::get('/products/slug/{slug}', [ProductController::class, 'getProductBySlug']);
Route::get('/products/rating/{slug}', [ProductController::class, 'getUserRating']);
Route::get('/products/category/{category}', [ProductController::class, 'productsByCategory']);
Route::get('/products/brand/{category}', [ProductController::class, 'productsByBrand']);
//rating
Route::get('/products/average-rating/{id}', [ProductController::class, 'averageRating']);
//delivery locations

Route::get('/delivery-options', [DeliveryOptionController::class, 'index']);
Route::get('/delivery-options/search', [DeliveryOptionController::class, 'search']);
Route::get('/delivery-options/show/{id}', [DeliveryOptionController::class, 'show']);
Route::get('/delivery-options/count', [DeliveryOptionController::class, 'count']);
