<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\WalletTransactionController ;
use App\Http\Controllers\Admin\NotificationTemplateController ;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PayPalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ==================== Admin Routes ====================

// Route::fallback(function () {
//     return redirect('superadmin');
// });



Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Google Login
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

//PaypalPayment Gateway
Route::get('paypal', [PayPalController::class, 'index'])->name('paypal.index');
Route::post('paypal/create', [PayPalController::class, 'createOrder'])->name('paypal.create');
Route::get('paypal/success/{referenceId}', [PayPalController::class, 'success'])->name('paypal.success');
Route::get('paypal/cancel/{referenceId}', [PayPalController::class, 'cancel'])->name('paypal.cancel');


Route::group(['prefix' => 'superadmin'], function() {
    Route::group(['middleware' => 'admin.guest'], function(){ 

        Route::view('/', 'admin.login')->name('admin.login'); 
        Route::view('/forgot-password', 'admin.forgot-password')->name('admin.forgot-password'); 
        Route::post('/login', [AdminController::class, 'authenticate'])->name('admin.auth');
        Route::post('/check-email', [AdminController::class, 'checkEmail'])->name('admin.checkEmail');
        Route::post('/set-reset-email', [AdminController::class, 'setResetEmail'])->name('admin.setResetEmail'); // 🔹 new route
        Route::get('/reset-password', [AdminController::class, 'viewResetPasswordPage'])->name('admin.resetPassword');
        Route::post('/reset-password', [AdminController::class, 'resetPasswordSubmit'])->name('admin.resetPasswordSubmit');


    });

    // ------------------ Authenticated Admin Routes ------------------
    Route::group(['middleware' => 'admin.auth'], function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('superadmin.dashboard');
        Route::get('/logout', [AdminController::class, 'logout'])->name('superadmin.logout'); 

    // =====================USER===================================
        Route::get('/user-list', [AdminController::class, 'userList'])->name('superadmin.user.list.page');
        Route::get('/get-user-list', [AdminController::class, 'getUserList'])->name('superadmin.user.list');
        Route::post('admin/user/status', [AdminController::class, 'changeStatus'])->name('superadmin.user.status');
        Route::post('admin/user/delete', [AdminController::class, 'deleteUser'])->name('superadmin.user.delete');
        Route::post('admin/user/details', [AdminController::class, 'getUserDetails'])->name('superadmin.user.details');
        
        Route::get('user/view/{id}', [AdminController::class, 'viewUserDetails'])
        ->name('superadmin.user.view');
        Route::get('/user/{id}/transactions', [AdminController::class, 'userTransactionsView'])
            ->name('superadmin.user.transactions');
        Route::get('/user/{id}/transactions/data', [AdminController::class, 'userTransactionsData'])
        ->name('superadmin.user.transactions.data');
        Route::get('/user/{id}/subscriptions', [AdminController::class, 'userSubscriptionsView'])
            ->name('superadmin.user.subscriptions');
        Route::get('/user/{id}/subscriptions/data', [AdminController::class, 'userSubscriptionsData'])
            ->name('superadmin.user.subscriptions.data');   
        Route::post('/user/extend', [AdminController::class, 'extendSubscription'])
	        ->name('superadmin.user.extend');
        
    // =====================VIDEO===================================

        Route::get('/video-list', [VideoController::class, 'videoList'])->name('superadmin.video.list.page');
        Route::get('/video-create', [VideoController::class, 'videoCreate'])->name('superadmin.videos.create');
        Route::post('/video-store', [VideoController::class, 'videoStore'])->name('superadmin.video.store');
        Route::get('/get-video-list', [VideoController::class, 'getVideoList'])->name('superadmin.video.list');
        Route::post('/video/delete', [VideoController::class, 'deleteVideo'])->name('superadmin.video.delete');
        Route::post('/video/status', [VideoController::class, 'updateVideoStatus'])->name('superadmin.video.status');
        Route::get('/video/edit/{id}', [VideoController::class, 'videoEdit'])->name('superadmin.video.edit');
        Route::post('/video/update/{id}', [VideoController::class, 'updateVideoStore'])->name('superadmin.video.update');
        Route::get('video/view/{id}', [VideoController::class, 'getVideoDetails'])->name('superadmin.video.view');

        
    // =====================CATEGORY===================================
        Route::get('/category-list', [CategoryController::class, 'categoryList'])->name('superadmin.category.list.page');
        Route::get('/category-create', [CategoryController::class, 'categoryCreate'])->name('superadmin.category.create');
        Route::post('/category-store', [CategoryController::class, 'categoryStore'])->name('superadmin.category.store');
        Route::get('/get-category-list', [CategoryController::class, 'getCategoryList'])->name('superadmin.category.list');
        Route::post('/category/delete', [CategoryController::class, 'deleteCategory'])->name('superadmin.category.delete');
        Route::post('/category/status', [CategoryController::class, 'updateCategoryStatus'])->name('superadmin.category.status');
        Route::get('/category/edit/{id}', [CategoryController::class, 'categoryEdit'])->name('superadmin.category.edit');
        Route::post('/category/update/{id}', [CategoryController::class, 'updateCategoryStore'])->name('superadmin.category.update');
        Route::get('category/view/{id}', [CategoryController::class, 'getCategoryDetails'])->name('superadmin.category.view');

    // =====================CALL REPORTS===================================
        Route::get('/call-reports', [AdminController::class, 'callReports'])->name('superadmin.call.reports');
        Route::get('/pricing-list', [AdminController::class, 'pricingLists'])->name('superadmin.pricing.list');
        Route::get('/pricing-add', [AdminController::class, 'pricingAdd'])->name('superadmin.pricing.add');

    // =====================SUBSCRPTION===================================
        Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('superadmin.subscriptions.index');
        Route::get('subscriptions/list', [SubscriptionController::class, 'list'])->name('superadmin.subscriptions.list');
        Route::get('subscriptions/create', [SubscriptionController::class, 'create'])->name('superadmin.subscriptions.create');
        Route::post('subscriptions', [SubscriptionController::class, 'store'])->name('superadmin.subscriptions.store');
        Route::get('subscriptions/{id}/edit', [SubscriptionController::class, 'edit'])->name('superadmin.subscriptions.edit');
        Route::post('/subscriptions/update/{id}', [SubscriptionController::class, 'update'])->name('superadmin.subscriptions.update');
        Route::post('subscriptions/destroy', [SubscriptionController::class, 'destroy'])->name('superadmin.subscriptions.destroy');
        Route::post('subscriptions/toggle-status', [SubscriptionController::class, 'toggleStatus'])->name('superadmin.subscriptions.toggleStatus');
        Route::post('subscriptions/details', [SubscriptionController::class, 'subscriptionDetails'])->name('superadmin.subscriptions.details');
        Route::get('subscriptions/{id}/view', [SubscriptionController::class, 'view'])->name('superadmin.subscriptions.view');

    });

    // =====================PAYMENT REQUEST===================================
        Route::get('paymentRequest', [WalletTransactionController::class, 'index'])->name('superadmin.wallet.requests');
         Route::get('paymentRequest/list', [WalletTransactionController::class, 'list'])->name('superadmin.wallet.request.list');
        Route::post('paymentRequest/approve/{id}', [WalletTransactionController::class, 'approve'])->name('superadmin.wallet.request.approve');
        Route::post('paymentRequest/reject/{id}', [WalletTransactionController::class, 'reject'])->name('superadmin.wallet.request.reject');

    // =====================NOTIFICATION===================================
        Route::get('/notification-list', [NotificationTemplateController::class, 'notificationList'])->name('superadmin.notification.list.page');
        Route::get('/notification-create', [NotificationTemplateController::class, 'notificationCreate'])->name('superadmin.notification.create');
        Route::post('/notification-store', [NotificationTemplateController::class, 'notificationStore'])->name('superadmin.notification.store');
        Route::get('/get-notification-list', [NotificationTemplateController::class, 'getNotificationList'])->name('superadmin.notification.list');
        Route::post('/notification/delete', [NotificationTemplateController::class, 'deleteNotification'])->name('superadmin.notification.delete');
        Route::post('/notification/status', [NotificationTemplateController::class, 'updateNotificationStatus'])->name('superadmin.notification.status');
        Route::get('/notification/edit/{id}', [NotificationTemplateController::class, 'notificationEdit'])->name('superadmin.notification.edit');
        Route::post('/notification/update/{id}', [NotificationTemplateController::class, 'updateNotificationStore'])->name('superadmin.notification.update');
        Route::get('notification/view/{id}', [NotificationTemplateController::class, 'getNotificationDetails'])->name('superadmin.notification.view');

        Route::post('notification/details', [AdminController::class, 'getNotificationDetails'])->name('superadmin.notification.details');
        Route::get('notification/{id}/view', [NotificationTemplateController::class, 'view'])->name('superadmin.notification.view');

    // =====================SITE SETTING===================================

        Route::get('/setting', [AdminController::class, 'settingPage'])->name('superadmin.setting'); 
        Route::post('site-settings', [AdminController::class, 'siteSettingsUpdate'])
            ->name('superadmin.site.setting.update'); 

    // =====================PAYMENT REQUEST===================================

        Route::post('/wallet-payments/{id}/approve', [AdminController::class, 'approve'])
        ->name('superadmin.wallet-payments.approve');

        Route::post('/wallet-payments/{id}/reject', [AdminController::class, 'reject'])
        ->name('superadmin.wallet-payments.reject');

        Route::get('/wallet-payment/view/{id}', [AdminController::class, 'viewWithdrawalRequestDetails'])
        ->name('superadmin.wallet-payment.view');


    

});