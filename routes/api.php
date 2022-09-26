<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MaintenanceController;
use App\Http\Controllers\Api\ResourcesController;
use App\Http\Controllers\Api\StatusController;
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
Route::post('/auth/login', [AuthController::class, 'postLogin'])->middleware(['throttle_login']);
Route::post('/auth/register', [AuthController::class, 'postRegister']);
Route::post('/auth/password/reset/request', [AuthController::class, 'postPasswordResetRequest']);
Route::post('/auth/password/reset/submit', [AuthController::class, 'postPasswordResetSubmit']);

// Resources routes
Route::get('/resources/languages', [ResourcesController::class, 'getLanguages']);
Route::get('/resources/language-lines/{group}', [ResourcesController::class, 'getLanguageLinesByGroup']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/auth/email/verification', [AuthController::class, 'getEmailVerification']);
    Route::post('/auth/email/verification', [AuthController::class, 'postEmailVerification']);
});

Route::middleware(['auth:sanctum', 'email_verified'])->group(function () {
    // Maintenance routes
    Route::post('/maintenance/up', [MaintenanceController::class, 'postUp']);
    Route::post('/maintenance/down', [MaintenanceController::class, 'postDown']);

    // Auth routes
    Route::get('/auth/user', [AuthController::class, 'getUser']);
    Route::patch('/auth/user', [AuthController::class, 'patchUser']);
    Route::put('/auth/user/devices/{uuid}', [AuthController::class, 'putUserDevice']);
    Route::post('/auth/logout', [AuthController::class, 'postLogout']);
    Route::post('/auth/logout/all', [AuthController::class, 'postLogoutAll']);
    Route::post('/auth/password/change', [AuthController::class, 'postPasswordChange']);
});
