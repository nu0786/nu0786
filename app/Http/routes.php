<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// //Adding Prefix as API routes will look like {{URL}}/api
// Route::group(['prefix' => 'api'], function () {

//     //Register Api For Registering New User
//     Route::post('register', 'UserController@register');

//     //Login Api For Login Authenticated User
//     Route::post('login', 'UserController@authenticate');

//     //JWT Middleware to authenticate token Before accessing apis
//     Route::group(['middleware' => ['jwt.verify']], function() {

//         //User Api To fetch the data of logged in User from access token
//         Route::get('user', 'UserController@getAuthenticatedUser');

//         //Logout Api which makes The logged in User Token Invalid
//         Route::get('user/logout', 'UserController@logout');

//         //Api To create loan for logged in User
//         Route::post('create/loan','LoanController@createUserLoan');
//     });
// });
