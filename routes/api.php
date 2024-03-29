<?php

use Illuminate\Http\Request;

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

Route::group(['prefix' =>'v1'],function(){

        Route::get('categories','Api\MainController@categories');
        Route::get('cities','Api\MainController@cities');
        Route::get('governorates','Api\MainController@governorates');
        Route::get('regions_ajax','Api\MainController@ajax_region');
        Route::get('payment-methods','Api\MainController@paymentMethods');
        Route::get('categories','Api\MainController@categories');
        Route::get('restaurants','Api\MainController@restaurants');
        Route::get('restaurant','Api\MainController@restaurant');
        Route::get('items','Api\MainController@items');
        Route::get('restaurant/reviews','Api\MainController@reviews');
        Route::get('offers','Api\MainController@offers');
        Route::get('offer','Api\MainController@offer');
        Route::post('contact','Api\MainController@contact');

  Route::group(['prefix' =>'client'],function(){

        Route::post('register', 'Api\Client\AuthController@register');
        Route::post('login', 'Api\Client\AuthController@login');
        Route::post('profile', 'Api\Client\AuthController@profile');
        Route::post('reset-password', 'Api\Client\AuthController@reset');
        Route::post('new-password', 'Api\Client\AuthController@password');

  Route::group(['middleware'=>'auth:api'],function(){

        Route::post('profile', 'Api\Client\AuthController@profile');
        Route::post('register-token', 'Api\Client\AuthController@registerToken');
        Route::post('remove-token', 'Api\Client\AuthController@removeToken');
        Route::post('add-item-to-cart','Api\Client\MainController@addItemToCart');
        Route::post('delete-item-from-cart','Api\Client\MainController@deleteItemFromCart');
        Route::post('delete-all-cart-items','Api\Client\MainController@deleteAllCartItems');
        Route::post('update-cart-item','Api\Client\MainController@updateCartItem');
        Route::get('get-cart-items','Api\Client\MainController@cartItems');
        Route::post('new-order','Api\Client\MainController@newOrder');
        Route::get('my-orders','Api\Client\MainController@myOrders');
        Route::get('show-order','Api\Client\MainController@showOrder');
        Route::get('latest-order','Api\Client\MainController@latestOrder');
        Route::post('confirm-order','Api\Client\MainController@confirmOrder');
        Route::post('decline-order','Api\Client\MainController@declineOrder');
        Route::post('restaurant/review','Api\Client\MainController@review');
        Route::get('notifications','Api\Client\MainController@notifications');
        });
    }); 
  Route::group(['prefix' =>'restaurant'],function(){

        Route::post('register', 'Api\Restaurant\AuthController@register');
        Route::post('login', 'Api\Restaurant\AuthController@login');
        Route::post('profile', 'Api\Restaurant\AuthController@profile');
        Route::post('reset-password', 'Api\Restaurant\AuthController@reset');
        Route::post('new-password', 'Api\Restaurant\AuthController@password');

  Route::group(['middleware'=>'auth:restaurant'],function(){

        Route::post('profile', 'Api\Restaurant\AuthController@profile');
        Route::post('change-state','Api\Restaurant\MainController@changeState');
        Route::post('register-token', 'Api\Restaurant\AuthController@registerToken');
        Route::post('remove-token', 'Api\Restaurant\AuthController@removeToken');
        Route::get('my-items','Api\Restaurant\MainController@myItems');
        Route::post('new-item','Api\Restaurant\MainController@newItem');
        Route::post('update-item','Api\Restaurant\MainController@updateItem');
        Route::post('delete-item','Api\Restaurant\MainController@deleteItem');
        Route::get('my-offers','Api\Restaurant\MainController@myOffers');
        Route::post('new-offer','Api\Restaurant\MainController@newOffer');
        Route::post('update-offer','Api\Restaurant\MainController@updateOffer');
        Route::post('delete-offer','Api\Restaurant\MainController@deleteOffer');
        Route::get('my-orders','Api\Restaurant\MainController@myOrders');
        Route::get('show-order','Api\Restaurant\MainController@showOrder');
        Route::post('confirm-order','Api\Restaurant\MainController@confirmOrder');
        Route::post('accept-order','Api\Restaurant\MainController@acceptOrder');
        Route::post('reject-order','Api\Restaurant\MainController@rejectOrder');
        Route::get('notifications','Api\Restaurant\MainController@notifications');
        Route::post('change-state','Api\Restaurant\MainController@changeState');
        Route::get('commissions','Api\Restaurant\MainController@commissions');
        });
    });


});