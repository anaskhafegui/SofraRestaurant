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

Route::post('register', 'Api\AuthController@register');
Route::post('login', 'Api\AuthController@login');
Route::post('reset', 'Api\AuthController@reset');
Route::post('newpassword', 'Api\AuthController@newpassword');


Route::get('restaurants', 'Api\RestaurantController@index');
Route::post('restaurants/filter', 'Api\RestaurantController@filter');
Route::get('restaurants/show/{id}', 'Api\RestaurantController@show');
Route::post('restaurants/show/{id}/reviews', 'Api\RestaurantController@review');
Route::get('restaurants/show/{id}/item', 'Api\RestaurantController@item');





Route::group(['middleware' => 'auth:api',], function () {

   
   

    Route::post('restaurants/item/cart', 'Api\RestaurantController@addItemToCart');
    Route::get('restaurants/item/cart', 'Api\RestaurantController@allClientCart');
    Route::put('restaurants/item/cart/{id}', 'Api\RestaurantController@updateItemToCart');
    Route::delete('restaurants/item/cart/{id}', 'Api\RestaurantController@removeItemFromCart');
    Route::put('restaurants/item/cart/{id}/confirmed', 'Api\RestaurantController@ConfirmedCart');


    Route::post('restaurants/item/cart', 'Api\RestaurantController@PaymentMethod');
  
});
