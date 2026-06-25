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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//new website order api route
Route::group([
    'middleware' => 'log.request',
    'prefix' => 'order'
], function ($router) {
    Route::post('get',[App\Http\Controllers\NewSiteController\OrderController::class,'generateLeadNew'])->middleware('log.request')->name('order-get');

});

Route::post('razorpay-pay-req',[App\Http\Controllers\NewSiteController\OrderController::class,'razorpayPaymentApi'])->middleware('log.request')->name('razorpay-pay-req');

Route::any('razorpay-callback-url',[App\Http\Controllers\NewSiteController\OrderController::class,'razorpayPaymentApiRes'])->middleware('log.request')->name('razorpay-pay-req-res');

Route::any('poptin-submit',[App\Http\Controllers\NewSiteController\PoptinControlller::class,'submitData'])->middleware('log.request')->name('poptin-submit');


Route::group([
    'middleware' => 'log.request',
    'prefix' => 'q5capp-av1'
],function($router){
    Route::post('validate-user',[App\Http\Controllers\AppApi\AuthController::class,'validateUser'])->middleware('log.request')->name('validate-user');
    Route::post('dashboard',[App\Http\Controllers\AppApi\OrdersController::class,'dashboard'])->middleware('api.authentication')->name('dashboard');
    Route::get('orders',[App\Http\Controllers\AppApi\OrdersController::class,'fetchOrders'])->middleware('api.authentication')->name('orders');
    Route::post('order-details',[App\Http\Controllers\AppApi\OrdersController::class,'fetchOrderDetails'])->middleware('api.authentication')->name('order-details');
    Route::post('update-status',[App\Http\Controllers\AppApi\OrdersController::class,'updateStatus'])->middleware('api.authentication')->name('update-status');
    Route::post('upload-image',[App\Http\Controllers\AppApi\OrdersController::class,'uploadImage'])->middleware('api.authentication')->name('upload-image');
    Route::post('update-payment',[App\Http\Controllers\AppApi\OrdersController::class,'updatePayment'])->middleware('api.authentication')->name('update-payment');
    Route::post('update-feedback',[App\Http\Controllers\AppApi\OrdersController::class,'updateFeedback'])->middleware('api.authentication')->name('update-feedback');
    Route::post('order-expense-details',[App\Http\Controllers\AppApi\OrdersController::class,'orderExpenseDetails'])->middleware('api.authentication')->name('order-expense-details');
    Route::post('exp-date-list',[App\Http\Controllers\AppApi\ExpensesController::class,'expDateList'])->middleware('api.authentication')->name('exp-date-list');
    Route::post('expense-details',[App\Http\Controllers\AppApi\ExpensesController::class,'expenseDetails'])->middleware('api.authentication')->name('expense-details');
    Route::post('update-expense',[App\Http\Controllers\AppApi\ExpensesController::class,'updateExpense'])->middleware('api.authentication')->name('update-expense');
    Route::post('about-us',[App\Http\Controllers\AppApi\OrdersController::class,'aboutUs'])->name('about-us');
});


Route::group([
    'middleware' => 'log.request',
    'prefix' => 'q5capp-av2'
],function($router){
    Route::post('validate-user',[App\Http\Controllers\AppApiV2\AuthController::class,'validateUser'])->middleware('log.request')->name('validate-user');
    Route::post('dashboard',[App\Http\Controllers\AppApiV2\OrdersController::class,'dashboard'])->middleware('api.authenticationv2')->name('dashboard');
    Route::get('orders',[App\Http\Controllers\AppApiV2\OrdersController::class,'fetchOrders'])->middleware('api.authenticationv2')->name('orders');
    Route::post('order-details',[App\Http\Controllers\AppApiV2\OrdersController::class,'fetchOrderDetails'])->middleware('api.authenticationv2')->name('order-details');
    Route::post('update-status',[App\Http\Controllers\AppApiV2\OrdersController::class,'updateStatus'])->middleware('api.authenticationv2')->name('update-status');
    Route::post('upload-image',[App\Http\Controllers\AppApiV2\OrdersController::class,'uploadImage'])->middleware('api.authenticationv2')->name('upload-image');
    Route::post('update-payment',[App\Http\Controllers\AppApiV2\OrdersController::class,'updatePayment'])->middleware('api.authenticationv2')->name('update-payment');
    Route::post('update-feedback',[App\Http\Controllers\AppApiV2\OrdersController::class,'updateFeedback'])->middleware('api.authenticationv2')->name('update-feedback');
    Route::post('order-expense-details',[App\Http\Controllers\AppApiV2\OrdersController::class,'orderExpenseDetails'])->middleware('api.authenticationv2')->name('order-expense-details');
    Route::post('exp-date-list',[App\Http\Controllers\AppApiV2\ExpensesController::class,'expDateList'])->middleware('api.authenticationv2')->name('exp-date-list');
    Route::post('expense-details',[App\Http\Controllers\AppApiV2\ExpensesController::class,'expenseDetails'])->middleware('api.authenticationv2')->name('expense-details');
    Route::post('update-expense',[App\Http\Controllers\AppApiV2\ExpensesController::class,'updateExpense'])->middleware('api.authenticationv2')->name('update-expense');
    Route::post('about-us',[App\Http\Controllers\AppApiV2\OrdersController::class,'aboutUs'])->name('about-us');
    Route::post('order-summary',[App\Http\Controllers\AppApiV2\ReportsController::class,'orderSummary'])->name('order-summary');
    Route::post('fetch-profile',[App\Http\Controllers\AppApiV2\AuthController::class,'fetchProfile'])->middleware('api.authentication')->name('fetch-profile');
});