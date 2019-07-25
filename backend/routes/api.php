<?php

use Illuminate\Http\Request;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: X-Requested-With, content-type, X-Token, x-token, authorization');

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

//Route::group(['prefix' => 'v1', 'middleware' => ['cors']], function(){
Route::group(['prefix' => 'v1'], function(){

    Route::get('info', function(){
        return [
            'info' => 'Welcome to Wordpay API',
            'description' => 'Welcome to Wordpay API',
            'version' => '1.0',
            'company' => 'Wordpay',
            'copyright' => 'Mindtask'
        ];
    });
    
    Route::post('login', 'API\v1\UserController@login');
    Route::post('register', 'API\v1\UserController@register');
    Route::post('social-login', 'API\v1\UserController@social_login');
    Route::post('apikey/validate',  'API\v1\ApikeyController@apikey_validate');
    
    Route::post('forgot-password', 'API\v1\UserController@forgot_password');
    Route::post('reset-password/{token?}', 'API\v1\UserController@reset_password');
    Route::get('user/activation/{token?}', 'API\v1\UserController@user_activation');

    Route::group(['middleware' => 'auth:api'], function(){

        Route::group(['middleware' => 'media_user'], function(){
            // Media User API'S
            Route::get('media-graph-data/{companyId?}',          'API\v1\UserController@mediaGraphData');
        });

        Route::group(['middleware' => 'admin_user'], function(){
            // Admin User API'S
            Route::get('admin-dashboard-graph-data',          'API\v1\UserController@adminDashboardGraphData');
        });
        

        Route::get('user',          'API\v1\UserController@details');
        Route::put('user/{id?}',    'API\v1\UserController@updateUser');
        Route::get('user/coins',    'API\v1\UserController@getCoins');
        Route::post('user/coins',    'API\v1\UserController@getCoins');
        Route::get('user/logout',          'API\v1\UserController@logout');

        Route::resource('company', 'API\v1\CompanyController');
        Route::post('company/change-status', 'API\v1\CompanyController@changeStatus');
        Route::post('company/update-payout-details', 'API\v1\CompanyController@updatePayoutDetails');
        

        Route::post('articles',    'API\v1\ArticleController@read');
        //Route::resource('articles', 'API\v1\ArticleController');
        Route::get('articles', 'API\v1\ArticleController@index');

        
        Route::resource('card',    'API\v1\CardController');
        Route::get('card/user/{userId}',    'API\v1\CardController@userCardList');
        
        Route::resource('bank',    'API\v1\BankController');
        Route::get('bank/company/{companyId}',    'API\v1\BankController@companyBankList');

        Route::resource('country', 'API\v1\CountryController');

        Route::resource('apikey',  'API\v1\ApikeyController');
        Route::get('apikey/details', 'API\v1\ApikeyController@count_api_details');
        Route::resource('package', 'API\v1\PackageController');
        
        Route::resource('revenue', 'API\v1\RevenueController');
        Route::get('revenue/company/{companyId}',    'API\v1\RevenueController@companyRevenueList');

        Route::resource('coins',   'API\v1\CoinController');
        
        Route::resource('payout', 'API\v1\PayoutController');
        Route::get('payout/company/{companyId}',    'API\v1\PayoutController@companyPayoutList');
        //Next payout header
        Route::get('nextpayout',    'API\v1\UserController@nextPayout');
        //Next payout header
        Route::get('dopayout',    'API\v1\UserController@doPayout');
    });


    Route::post('wordpress/revenue',    'API\v1\RevenueController@wordpressCompanyRevenueList');
    Route::post('country/wordpress', 'API\v1\CountryController@wordpressCountryList');
});
