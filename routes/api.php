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
    Route::post('social-signup','AuthController@socialSignUp');

    // Route::post('register-hairstylist',"AuthController@registerAsHairstylist");
});

Route::group([
    "middleware" => "auth:api"
], function() {
    // Route::get('user/hairstylist','AuthController@getHairStylist');

    Route::get('user/favorite','UserFavoriteController@getFavorites');
    Route::post('user/favorite','UserFavoriteController@postFavorite');
    Route::post('user/edit','UserEditController@userUpdate');
    Route::get('user/edit','UserEditController@userEdit');

    //user profile update route//
    Route::post('user/edit-profile','UserController@editUserProfile');
    
    //user salon update route//

    Route::post('user/edit-salon','UserController@editUserSalon');
    
    
    
    Route::delete('user/image/{id}','UserEditController@deleteImage');
    
    Route::get('user/rating/{id}','UserRatingController@userRating');
    Route::post('user/rating',"UserRatingController@postUserRating");
    
    
});
//     Route::get('hairstylists',function(){
    //             return 'hair stylist route';
    //    });
    Route::get('hairstylists','HairStylistController@hairStylists');
    Route::get('getusers','UserController@getUser');
    Route::post('user/forgotpassword','ForgotPasswordController@index');
    Route::post('user/verify','EmailVerificationController@sendEmail');
    Route::post('register-hairstylist',"AuthController@registerAsHairstylist");
//  Route::post('social-signup','AuthController@socialSigcUp');

 //save kar, yae route meny auth mai dala hua hai

