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

//Adding Prefix as API routes will look like {{URL}}/api
Route::group(['prefix' => 'api'], function () {

    //Register Api For Registering New User
    Route::post('register', 'UserController@register');

    //Login Api For Login Authenticated User
    Route::post('login', 'UserController@authenticate');

    //JWT Middleware to authenticate token Before accessing apis
    Route::group(['middleware' => ['jwt.verify']], function() {

        //User Api To fetch the data of logged in User from access token
        Route::get('user', 'UserController@getAuthenticatedUser');

        //User Api To Authorized User To Approve Loan
        Route::put('authorize_user/{users_id}', 'UserController@authorizeUser');

        //Logout Api which makes The logged in User Token Invalid
        Route::get('user/logout', 'UserController@logout');

        Route::group(['prefix' => 'loan'], function () {

            //Api To create loan for logged in User
            Route::post('create','LoanController@createUserLoan');

            //Api To get loan details for User
            Route::get('loan_details','LoanController@getLoanDetails');

            //Api To approve loan for User
            Route::put('approve_loan/{loan_id}','LoanController@approveUserLoan');

            //Api To reject loan for User
            Route::put('reject_loan/{loan_id}','LoanController@rejectUserLoan');

            //Api To process loan payment
            Route::post('payment','LoanController@processLoanPayment');

        });
    });
});
