<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Auth;
use App\Http\Controllers\Api\MaintenanceController;
use App\Http\Controllers\Api\ResourcesController;
use App\Http\Controllers\Api\StatusController;
use Illuminate\Support\Facades\Route;

Route::middleware(['block-in-production'])->group(function () {
    Route::get('/', [ApiController::class, 'getApi'])->name('get.api.ui');
    Route::get('/api/documentation', [ApiController::class, 'getApiDocumentation'])->name('get.api.documentation');
});

Route::get('/status', [StatusController::class, 'getStatus']);

// Auth routes
Route::post('/auth/login', [Auth\PublicController::class, 'postLogin'])->middleware(['throttle_login']);
Route::post('/auth/register', [Auth\PublicController::class, 'postRegister']);
Route::post('/auth/password/reset/request', [Auth\PublicController::class, 'postPasswordResetRequest']);
Route::post('/auth/password/reset/submit', [Auth\PublicController::class, 'postPasswordResetSubmit']);

// Resources routes
Route::get('/resources/languages', [ResourcesController::class, 'getLanguages']);
Route::get('/resources/language-lines/{group}', [ResourcesController::class, 'getLanguageLines']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/auth/email/verification', [Auth\PrivateController::class, 'getEmailVerification']);
    Route::post('/auth/email/verification', [Auth\PrivateController::class, 'postEmailVerification']);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Maintenance routes
    Route::post('/maintenance/up', [MaintenanceController::class, 'postUp']);
    Route::post('/maintenance/down', [MaintenanceController::class, 'postDown']);

    // Auth routes
    Route::get('/auth/user', [Auth\PrivateController::class, 'getUser']);
    Route::patch('/auth/user', [Auth\PrivateController::class, 'patchUser']);
    Route::put('/auth/user/devices/{uuid}', [Auth\PrivateController::class, 'putUserDevice']);
    Route::post('/auth/logout', [Auth\PrivateController::class, 'postLogout']);
    Route::post('/auth/logout/all', [Auth\PrivateController::class, 'postLogoutAll']);
    Route::post('/auth/password/change', [Auth\PrivateController::class, 'postPasswordChange']);
});
