<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\ContinueWatchingController;


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
Route::middleware('auth:sanctum')->group(function () {
    // User profile after login
    Route::get('userProfileByToken', [ProfileController::class, 'userProfileByTokens']);

    // 👇 check user subscription API
    Route::get('check-subscription', [ProfileController::class, 'checkSubscriptionForUser']);

    // 👇 Videos related api
    // Get categories with videos (optional categoryId)
    Route::get('video/categories', [VideoController::class, 'categories']);
    // Get categories with videos (optional categoryId)
    Route::get('video/category/{id}/videos', [VideoController::class, 'videosByCategory']);
    // Get categories with videos (optional categoryId)
    Route::get('video/categories-with-videos/{categoryId?}', [VideoController::class, 'categoriesWithVideos']);
    // Get  videos Details 
    Route::get('video/{id}', [VideoController::class, 'getVideoDetails']);
    Route::get('videos/trending', [VideoController::class, 'trendingVideos']);

    //Continue Watching
    Route::post('/continue-watching/update', [ContinueWatchingController::class, 'updateProgress']);
    Route::get('/continue-watching/get', [ContinueWatchingController::class, 'getProgress']);
    Route::get('/continue-watching/list', [ContinueWatchingController::class, 'list']);

    //Offline Downloaded Videos
    Route::post('/video/offline/save', [OfflineDownloadController::class, 'saveDownload']);
    Route::post('/video/offline/remove', [OfflineDownloadController::class, 'removeDownload']);
    Route::get('/video/offline/list', [OfflineDownloadController::class, 'listDownloads']);


});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('/register', [AuthController::class, 'register']);
Route::post('/socialLoginRegister', [AuthController::class, 'socialLoginRegister']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', function (Request $request) {
        return $request->user();
    });

    Route::post('/wallet/withdrawal', [WalletController::class, 'requestWithdrawal']);

});
