<?php

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

Route::get('/', 'Api\ApiController@getDocumentation')->name('api');
Route::get('/docs', 'Api\ApiController@getDocs')->name('docs');
Route::get('/status', 'Api\ApiController@getStatus');

// Auth routes
Route::post('/auth/login', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken')->middleware(['set_oauth_client', 'sanitize_login', 'throttle_login']);
Route::post('/auth/refresh', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken')->middleware(['set_oauth_client', 'sanitize_refresh', 'throttle_login']);
Route::post('/auth/register', 'Api\AuthController@postRegister');
Route::post('/auth/password/reset/request', 'Api\AuthController@postPasswordResetRequest');
Route::post('/auth/password/reset/submit', 'Api\AuthController@postPasswordResetSubmit');
Route::post('/auth/email/verification/request', 'Api\AuthController@postEmailVerificationRequest');
Route::post('/auth/email/verification/confirm', 'Api\AuthController@postEmailVerificationConfirm');

// Email routes
Route::get('/emails/{email}/read', 'Api\EmailController@getEmailRead')->name('email-read');

// Metadata routes
Route::get('/metadata/phrases/{type}', 'Api\MetadataController@getPhrases');
Route::get('/metadata/languages/{search_string?}', 'Api\MetadataController@getLanguagesSearch');

Route::middleware(['auth:api', 'email_verified'])->group(function () {
    // Maintenance routes
    Route::post('/maintenance/enable', 'Api\MaintenanceController@postEnable');
    Route::post('/maintenance/disable', 'Api\MaintenanceController@postDisable');

    // User routes
    Route::get('/user', 'Api\UserController@getUser');
    Route::patch('/user', 'Api\UserController@patchUser');
    Route::post('/user/devices', 'Api\UserController@postUserDevices');
    Route::post('/user/pushes/{push}', 'Api\UserController@postUserPush');
    Route::post('/user/logout', 'Api\UserController@postUserLogout');
    Route::post('/user/logout/all', 'Api\UserController@postUserLogoutAll');
    Route::post('/user/password/change', 'Api\UserController@postPasswordChange');
});
