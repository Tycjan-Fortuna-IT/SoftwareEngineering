<?php

use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

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

Route::get('/csrf-cookie', [CsrfCookieController::class, 'show'])->middleware('web')->name('sanctum.csrf-cookie');

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Consider:
// Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
// Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
// Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)->name('verification.verify');
// Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->name('verification.send');

Route::apiResource('/users', UserController::class)->names('api.users')->except(['store']);
