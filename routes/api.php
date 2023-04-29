<?php

use App\Helpers\ResponseHelper;
use App\Http\Controllers\V1\Auth\ApiKeyController;
use App\Http\Controllers\V1\Auth\LoginController;
use App\Http\Controllers\V1\Auth\LogoutController;
use App\Http\Controllers\V1\Auth\PasswordController;
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

Route::group(['prefix' => 'v1'], function () {
    //endpoint for login with phone number and password
    Route::post('login', [LoginController::class, 'login'])->name('login');
    //endpoint for logging in with api key
    Route::get('auth/key', [ApiKeyController::class, 'keyacess'])->name('auth.key');
    //endpoint for logout
    Route::post('/logout', [LogoutController::class, 'logout']);
    //endpoints for private and public key fetch all, store and destroy
    Route::get('/api-keys', [ApiKeyController::class, 'index'])->name('api_keys');
    //endpoint for deleting api key and secret
    Route::delete('/api-key/{api_key:uuid}/delete', [ApiKeyController::class, 'destroy'])->name('api_keys.destroy');

    //change-password Endpoint
    Route::get('/change-password', [PasswordController::class, 'changePassword'])->name('change.password');
    //forget-password Endpoint
    Route::post('/forget-password', [PasswordController::class, 'forgetPassword'])->name('forget.password');
    //reset password Endpoint
    Route::post('reset-password', [PasswordController::class, 'resetPassword'])->name('reset.password');
});

Route::fallback(function () {
    return ResponseHelper::error(404, 'Check the endpoint and retry');
});
