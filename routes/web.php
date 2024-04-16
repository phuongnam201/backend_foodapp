<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});


Route::group(['prefix' => 'payment-mobile'], function () {
    Route::get('/', '\App\Http\Controllers\PaymentController@payment')->name('payment-mobile');
    Route::get('set-payment-method/{name}', 'PaymentController@set_payment_method')->name('set-payment-method');
});


 /**vnpay work but often maintenance */
 Route::post('vnpay', '\App\Http\Controllers\VNPayController@payWithVnpay')->name('vnpay');
 Route::get('vnpay_return', '\App\Http\Controllers\VNPayController@handleVnpayReturn')->name('vnpay_return');
 Route::get('payment-status', '\App\Http\Controllers\VNPayController@showPaymentResult')->name('payment-status');
 Route::get('result-payment', '\App\Http\Controllers\VNPayController@resultPayment')->name('result-payment');

 /** payos real but costly :v */
 Route::post('payos', '\App\Http\Controllers\PayOsController@createPaymentLink')->name('payos');
 Route::get('payos_return', '\App\Http\Controllers\PayOsController@handlePayosReturn')->name('payos_return');

