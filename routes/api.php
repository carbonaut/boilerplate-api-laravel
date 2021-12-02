<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\MaintenanceController;
use App\Http\Controllers\Api\MetadataController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;

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

Route::get('/', [ApiController::class, 'getDocumentation'])->name('api');
Route::get('/docs', [ApiController::class, 'getDocs'])->name('docs');
Route::get('/status', [ApiController::class, 'getStatus']);

// Auth routes
Route::post('/auth/login', [AccessTokenController::class, 'issueToken'])->middleware(['set_oauth_client', 'sanitize_login', 'throttle_login']);
Route::post('/auth/refresh', [AccessTokenController::class, 'issueToken'])->middleware(['set_oauth_client', 'sanitize_refresh', 'throttle_login']);
Route::post('/auth/register', [AuthController::class, 'postRegister']);
Route::post('/auth/password/reset/request', [AuthController::class, 'postPasswordResetRequest']);
Route::post('/auth/password/reset/submit', [AuthController::class, 'postPasswordResetSubmit']);
Route::post('/auth/email/verification/request', [AuthController::class, 'postEmailVerificationRequest']);
Route::post('/auth/email/verification/confirm', [AuthController::class, 'postEmailVerificationConfirm']);

// Email routes
Route::get('/emails/{email}/read', [EmailController::class, 'getEmailRead'])->name('email-read');

// Metadata routes
Route::get('/metadata/phrases/{type}', [MetadataController::class, 'getPhrases']);
Route::get('/metadata/languages/{search_string?}', [MetadataController::class, 'getLanguagesSearch']);

Route::middleware(['auth:api', 'email_verified'])->group(function () {
    // Maintenance routes
    Route::post('/maintenance/enable', [MaintenanceController::class, 'postEnable']);
    Route::post('/maintenance/disable', [MaintenanceController::class, 'postDisable']);

    // User routes
    Route::get('/user', [UserController::class, 'getUser']);
    Route::patch('/user', [UserController::class, 'patchUser']);
    Route::post('/user/devices', [UserController::class, 'postUserDevices']);
    Route::post('/user/pushes/{push}', [UserController::class, 'postUserPush']);
    Route::post('/user/logout', [UserController::class, 'postUserLogout']);
    Route::post('/user/logout/all', [UserController::class, 'postUserLogoutAll']);
    Route::post('/user/password/change', [UserController::class, 'postPasswordChange']);
});
