<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ResourcesController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\MaintenanceController;
use App\Http\Controllers\Api\MetadataController;
use App\Http\Controllers\Api\UserController;
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

Route::middleware(['block-in-production'])->group(function () {
    Route::get('/', [ApiController::class, 'getApi'])->name('get.api.ui');
    Route::get('/api/documentation', [ApiController::class, 'getApiDocumentation'])->name('get.api.documentation');
});

Route::get('/status', [StatusController::class, 'getStatus']);

// Auth routes
Route::post('/auth/login', [AuthController::class, 'postLogin']); // ->middleware(['throttle_login']);
Route::post('/auth/register', [AuthController::class, 'postRegister']);
Route::post('/auth/password/reset/request', [AuthController::class, 'postPasswordResetRequest']);
Route::post('/auth/password/reset/submit', [AuthController::class, 'postPasswordResetSubmit']);
Route::post('/auth/email/verification/request', [AuthController::class, 'postEmailVerificationRequest']);
Route::post('/auth/email/verification/confirm', [AuthController::class, 'postEmailVerificationConfirm']);

// Email routes
Route::get('/emails/{email}/read', [EmailController::class, 'getEmailRead'])->name('email-read');

// Resources routes
Route::get('/resources/languages', [ResourcesController::class, 'getLanguages']);
Route::get('/resources/language-lines/{group}', [ResourcesController::class, 'getLanguageLinesByGroup']);

Route::middleware(['auth:sanctum', 'email_verified'])->group(function () {
    // Maintenance routes
    Route::post('/maintenance/enable', [MaintenanceController::class, 'postEnable']);
    Route::post('/maintenance/disable', [MaintenanceController::class, 'postDisable']);

    // User routes
    Route::get('/auth/user', [AuthController::class, 'getUser']);
    Route::patch('/user', [UserController::class, 'patchUser']);
    Route::post('/user/devices', [UserController::class, 'postUserDevices']);
    Route::post('/user/pushes/{push}', [UserController::class, 'postUserPush']);
    Route::post('/auth/logout', [AuthController::class, 'postLogout']);
    Route::post('/auth/logout/all', [AuthController::class, 'postLogoutAll']);
    Route::post('/user/password/change', [UserController::class, 'postPasswordChange']);
});
