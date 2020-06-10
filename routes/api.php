<?php

Route::group([
    'prefix' => 'auth'

], function () {
    Route::post('login', 'AuthController@login');
    Route::get('login', function() {
        return "Unauthorized";
    })->name('login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('register','AuthController@register');
});

Route::group([
    "middleware" => "auth:api"
], function() {
    Route::get('user/hairstylist','AuthController@getHairStylist');

    Route::get('user/favorite','UserFavoriteController@getFavorites');
    Route::post('user/favorite','UserFavoriteController@postFavorite');
    Route::post('user/edit','UserEditController@userUpdate');
    Route::get('user/edit','UserEditController@userEdit');


    Route::delete('user/image/{id}','UserEditController@deleteImage');

    Route::get('user/rating/{id}','UserRatingController@userRating');
    Route::post('user/rating',"UserRatingController@postUserRating");
});