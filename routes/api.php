<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\Auth\CustomerAuthController;
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

Route::prefix('/v1')->group(function () {
    Route::prefix('products')->group(function () {
        Route::get('/', [FoodController::class, 'index']);
        Route::get('popular', [FoodController::class, 'getPopularItems']);
        Route::get('recommended', [FoodController::class, 'getRecommendedItems']);
        Route::get('search', [FoodController::class, 'search_products']);
    });

    Route::prefix('config')->group(function () {
        Route::get('geocode-api', [ConfigController::class, 'geocode_api']);
        Route::get('place-api-autocomplete', [ConfigController::class, 'place_api_autocomplete']);
        Route::get('place-api-details', [ConfigController::class, 'place_api_details']);
    });

    Route::prefix('auth')->namespace('Auth')->group(function () {
        Route::post('register', [CustomerAuthController::class, 'register']);
        Route::post('login', [CustomerAuthController::class, 'login']);
    });

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('/logout', [CustomerAuthController::class, 'logout']);
        Route::post('/forgot-password', [CustomerAuthController::class, 'forgot_password']);

        Route::prefix('customer')->group(function () {
            Route::prefix('order')->group(function () {
                Route::get('list', [OrderController::class, 'get_order_list']);
                Route::post('place', [OrderController::class, 'place_order']);
                Route::get('order_history', [OrderController::class, 'get_history_order']);
            });
        });
        Route::prefix('customer')->group(function () {
            Route::get('info', [CustomerController::class, 'info']);
            Route::get('address/list', [CustomerController::class, 'address_list']);
            Route::post('address/add', [CustomerController::class, 'add_new_address']);
            Route::post('address/update/{id}', [CustomerController::class, 'update_address']);
            Route::post('address/delete', [CustomerController::class, 'delete_address']);
        });
    });
});











// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });