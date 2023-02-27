<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Broadcast;
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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/google-login', [UserController::class, 'authGoogle']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/email/verify/{token}', [AuthController::class, 'verifyAccount'])->name('email.verify');
Route::post('/send/instructions', [UserController::class, 'sendInstructions']);
Route::post('/password/reset', [UserController::class, 'resetPassword']);
Route::get('/reset-password/{token}', [UserController::class, 'redirectToReset'])->name('password.reset');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/email/create', [UserController::class, 'addEmail']);
    Route::post('/email/delete', [UserController::class, 'deleteEmail']);
    Route::post('/email/make-primary', [UserController::class, 'makePrimary']);
    Route::post('/username/change', [UserController::class, 'changeUsername']);
    Route::post('/password/change', [UserController::class, 'changePassword']);
    Route::post('/upload/image', [UserController::class, 'uploadImage']);
    Route::get('/genres', [MovieController::class, 'genres']);
    Route::post('/movie/create', [MovieController::class, 'createMovie']);
    Route::get('/movie', [MovieController::class, 'allMovies']);
    Route::post('/movie/update', [MovieController::class, 'updateMovie']);
    Route::post('/movie/delete', [MovieController::class, 'deleteMovie']);
    Route::post('/quote/create', [QuoteController::class, 'createQuote']);
    Route::post('/quote/update', [QuoteController::class, 'updateQuote']);
    Route::post('/quote/delete', [QuoteController::class, 'deleteQuote']);
    Route::get('/quote', [QuoteController::class, 'allQuotes']);
    Route::post('/comment/add', [NotificationController::class, 'addComment']);
    Route::post('/like/add', [NotificationController::class, 'like']);
    Route::post('/like/remove', [NotificationController::class, 'unLike']);
    Route::post('/notification', [NotificationController::class, 'markAsRead']);
    Route::post('/notification/read', [NotificationController::class, 'readNotification']);
});

Broadcast::routes(['middleware' => ['auth:sanctum']]);
Route::get('/google/auth', [AuthController::class, 'redirectToGoogle']);
Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback']);
