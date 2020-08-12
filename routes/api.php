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

Route::group(['namespace' => 'API'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('register', 'AuthController@register');
        Route::post('login', 'AuthController@login');
    });

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::get('logout', 'AuthController@logout');
        });

        Route::group(['prefix' => 'user'], function () {
            Route::get('/', 'AuthController@getAuthenticatedUser');
            Route::post('update-profile', 'AuthController@updateProfile');
            Route::put('change-password', 'AuthController@changePassword');
            Route::get('tokens', 'AuthController@getAuthenticatedUserTokens');
        });

        Route::group(['prefix' => 'merchant'], function () {
            Route::post('store', 'MerchantController@store');
            Route::put('update', 'MerchantController@update');
            Route::post('store-photo', 'MerchantController@storePhoto');
            Route::get('all', 'MerchantController@getAll');
            Route::post('find-by-name', 'MerchantController@findByName');
            Route::post('find-by-category', 'MerchantController@findByCategory');
            Route::get('find-by-user', 'MerchantController@findByUser');
            Route::post('find-by-id', 'MerchantController@findById');
        });

        Route::group(['prefix' => 'queue'], function () {
            Route::post('store', 'QueueController@store');
            Route::post('find-by-date', 'QueueController@findByDate');
            Route::get('find-active-by-user', 'QueueController@findActiveByUser');
            Route::get('find-history-by-user', 'QueueController@findHistoryByUser');
            Route::get('count-waiting-by-merchant', 'QueueController@countWaitingByMerchant');
            Route::get('find-waiting-by-merchant', 'QueueController@findWaitingByMerchant');
            Route::get('find-history-by-merchant', 'QueueController@findHistoryByMerchant');
            Route::post('update-status', 'QueueController@updateStatus');
            Route::post('find-by-qrcode', 'QueueController@findByQRCode');
        });

        Route::group(['prefix' => 'service'], function () {
            Route::post('store', 'ServiceController@store');
            Route::put('update', 'ServiceController@update');
            Route::get('find-by-merchant', 'ServiceController@findByMerchant');
        });

        Route::group(['prefix' => 'business-hour'], function () {
            Route::post('update', 'BusinessHourController@update');
        });
    });
});
