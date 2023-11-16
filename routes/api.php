<?php

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\QuestController;
use App\Http\Controllers\API\TutorialController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Resources\API\UserResource;
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

Route::get('/csrf-cookie', [CsrfCookieController::class, 'show'])->middleware('web');

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    $user = $request->user();

    $user->load(['friends']);

    return new UserResource($user);
});

Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

// Consider:
// Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
// Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
// Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)->name('verification.verify');
// Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->name('verification.send');


Route::middleware(['auth:sanctum'])->group(function () {

    Route::apiResource('/users', UserController::class)->except(['store']);
    Route::post('/users/{user}/addFriend', [UserController::class, 'addFriend']);
    Route::delete('/users/{user}/removeFriend', [UserController::class, 'removeFriend']);
    Route::put('/users/{user}/updateFavourite', [UserController::class, 'updateFavourite']);

    Route::apiResource('/posts', PostController::class);

    Route::apiResource('/comments', CommentController::class);

    Route::apiResource('/tutorials', TutorialController::class)->only(['index', 'update']);

    Route::apiResource('/quests', QuestController::class);

});
