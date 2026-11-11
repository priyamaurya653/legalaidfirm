<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AdminSubscriptionController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CareerApplicationController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EnquiryController;
use App\Http\Controllers\Api\JobOpeningController;
use App\Http\Controllers\Api\LegalCaseController;
use App\Http\Controllers\Api\MySubscriptionController;
use App\Http\Controllers\Api\LoginLogController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PracticeAreaController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SubscriptionPlanController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SubscriptionMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes  (prefix: /api)  – Laravel Sanctum token based
|--------------------------------------------------------------------------
| Header:  Authorization: Bearer {token}
|          Accept: application/json
| Roles :  superadmin | admin (advocate) | user
*/

// ---------- Auth ----------
Route::prefix('auth')->name('api.auth.')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login');
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register');          // advocate
    Route::post('user-register', [AuthController::class, 'userRegister'])->middleware('throttle:5,1')->name('user-register'); // user

    Route::middleware(['auth:sanctum', RoleMiddleware::class . ':superadmin,admin,user'])->group(function () {
        Route::get('me', [AuthController::class, 'me'])->name('me');
        Route::get('login-history', [AuthController::class, 'loginHistory'])->name('login-history');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });
});

// ---------- Public (bina token) – website ke liye active practice areas ----------
Route::get('public/practice-areas', [PracticeAreaController::class, 'publicList'])->name('api.public.practice-areas');

// ---------- Website Contact form (public, bina token) ----------
Route::post('enquiries', [EnquiryController::class, 'store'])->middleware('throttle:5,1')->name('api.enquiries.store');

// ---------- My Profile (superadmin / admin / user – apni profile) ----------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':superadmin,admin,user'])->prefix('profile')->name('api.profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::post('/', [ProfileController::class, 'update'])->name('update');
    Route::post('password', [ProfileController::class, 'password'])->middleware('throttle:6,1')->name('password');
});

// ---------- Career (public, bina token) ----------
Route::get('public/jobs', [JobOpeningController::class, 'publicList'])->name('api.public.jobs');
Route::post('career/apply', [CareerApplicationController::class, 'apply'])->middleware('throttle:5,1')->name('api.career.apply');

// ---------- Superadmin only ----------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':superadmin'])->name('api.')->group(function () {
    Route::get('dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    // Admins (Advocates)
    Route::get('admins', [AdminController::class, 'index'])->name('admins.index');
    Route::post('admins', [AdminController::class, 'store'])->name('admins.store');
    Route::get('admins/{admin}', [AdminController::class, 'show'])->whereNumber('admin')->name('admins.show');
    Route::post('admins/{admin}', [AdminController::class, 'update'])->whereNumber('admin')->name('admins.update');
    Route::patch('admins/{admin}/status', [AdminController::class, 'updateStatus'])->whereNumber('admin')->name('admins.status');
    Route::delete('admins/{admin}', [AdminController::class, 'destroy'])->whereNumber('admin')->name('admins.destroy');

    // Users
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->whereNumber('user')->name('users.show');
    Route::post('users/{user}', [UserController::class, 'update'])->whereNumber('user')->name('users.update');
    Route::patch('users/{user}/status', [UserController::class, 'updateStatus'])->whereNumber('user')->name('users.status');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->whereNumber('user')->name('users.destroy');

    // Login activity
    Route::get('login-logs', [LoginLogController::class, 'index'])->name('login-logs.index');

    // Enquiries (Contact page form)
    Route::get('enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
    Route::get('enquiries/unread-count', [EnquiryController::class, 'unreadCount'])->name('enquiries.unread');
    Route::get('enquiries/{enquiry}', [EnquiryController::class, 'show'])->whereNumber('enquiry')->name('enquiries.show');
    Route::patch('enquiries/{enquiry}/status', [EnquiryController::class, 'updateStatus'])->whereNumber('enquiry')->name('enquiries.status');

    // Subscriptions – plans
    Route::get('subscription-plans', [SubscriptionPlanController::class, 'index'])->name('plans.index');
    Route::post('subscription-plans', [SubscriptionPlanController::class, 'store'])->name('plans.store');
    Route::get('subscription-plans/{plan}', [SubscriptionPlanController::class, 'show'])->whereNumber('plan')->name('plans.show');
    Route::post('subscription-plans/{plan}', [SubscriptionPlanController::class, 'update'])->whereNumber('plan')->name('plans.update');
    Route::patch('subscription-plans/{plan}/status', [SubscriptionPlanController::class, 'updateStatus'])->whereNumber('plan')->name('plans.status');
    Route::delete('subscription-plans/{plan}', [SubscriptionPlanController::class, 'destroy'])->whereNumber('plan')->name('plans.destroy');

    // Subscriptions – admins ko assign
    Route::get('subscriptions', [AdminSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('subscriptions', [AdminSubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::post('subscriptions/{subscription}/cancel', [AdminSubscriptionController::class, 'cancel'])->whereNumber('subscription')->name('subscriptions.cancel');
    Route::delete('subscriptions/{subscription}', [AdminSubscriptionController::class, 'destroy'])->whereNumber('subscription')->name('subscriptions.destroy');
    Route::get('subscription-requests', [AdminSubscriptionController::class, 'requests'])->name('subscription-requests.index');
    Route::patch('subscription-requests/{subscriptionRequest}', [AdminSubscriptionController::class, 'updateRequest'])->whereNumber('subscriptionRequest')->name('subscription-requests.update');

    // Career – Job Openings
    Route::get('jobs', [JobOpeningController::class, 'index'])->name('jobs.index');
    Route::post('jobs', [JobOpeningController::class, 'store'])->name('jobs.store');
    Route::get('jobs/{job}', [JobOpeningController::class, 'show'])->whereNumber('job')->name('jobs.show');
    Route::post('jobs/{job}', [JobOpeningController::class, 'update'])->whereNumber('job')->name('jobs.update');
    Route::patch('jobs/{job}/status', [JobOpeningController::class, 'updateStatus'])->whereNumber('job')->name('jobs.status');
    Route::delete('jobs/{job}', [JobOpeningController::class, 'destroy'])->whereNumber('job')->name('jobs.destroy');

    // Career – Applications (website form)
    Route::get('applications', [CareerApplicationController::class, 'index'])->name('applications.index');
    Route::get('applications/{application}', [CareerApplicationController::class, 'show'])->whereNumber('application')->name('applications.show');
    Route::patch('applications/{application}', [CareerApplicationController::class, 'update'])->whereNumber('application')->name('applications.update');
    Route::get('applications/{application}/resume', [CareerApplicationController::class, 'resume'])->whereNumber('application')->name('applications.resume');
    Route::delete('applications/{application}', [CareerApplicationController::class, 'destroy'])->whereNumber('application')->name('applications.destroy');

    // Practice Areas (dynamic)
    Route::get('practice-areas', [PracticeAreaController::class, 'index'])->name('practice-areas.index');
    Route::get('practice-areas/types', [PracticeAreaController::class, 'types'])->name('practice-areas.types');
    Route::post('practice-areas', [PracticeAreaController::class, 'store'])->name('practice-areas.store');
    Route::get('practice-areas/{practiceArea}', [PracticeAreaController::class, 'show'])->whereNumber('practiceArea')->name('practice-areas.show');
    Route::post('practice-areas/{practiceArea}', [PracticeAreaController::class, 'update'])->whereNumber('practiceArea')->name('practice-areas.update');
    Route::patch('practice-areas/{practiceArea}/status', [PracticeAreaController::class, 'updateStatus'])->whereNumber('practiceArea')->name('practice-areas.status');
    Route::delete('practice-areas/{practiceArea}', [PracticeAreaController::class, 'destroy'])->whereNumber('practiceArea')->name('practice-areas.destroy');
});

// ---------- Cases – superadmin (sab) + admin/advocate (sirf apne assigned, read only) ----------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':superadmin,admin'])->prefix('cases')->name('api.cases.')->group(function () {
    Route::get('/', [LegalCaseController::class, 'index'])->name('index');
    Route::get('options', [LegalCaseController::class, 'options'])->name('options');
    Route::get('{legalCase}', [LegalCaseController::class, 'show'])->whereNumber('legalCase')->name('show');
    Route::get('{legalCase}/files/{type}', [LegalCaseController::class, 'file'])->whereNumber('legalCase')->whereIn('type', ['aadhaar', 'pan'])->name('file');
    Route::get('{legalCase}/documents/{document}', [LegalCaseController::class, 'document'])->whereNumber(['legalCase', 'document'])->scopeBindings()->name('document');

    // superadmin only – add / edit / delete
    Route::middleware(RoleMiddleware::class . ':superadmin')->group(function () {
        Route::post('/', [LegalCaseController::class, 'store'])->name('store');
        Route::post('{legalCase}', [LegalCaseController::class, 'update'])->whereNumber('legalCase')->name('update');
        Route::delete('{legalCase}', [LegalCaseController::class, 'destroy'])->whereNumber('legalCase')->name('destroy');
        Route::delete('{legalCase}/documents/{document}', [LegalCaseController::class, 'destroyDocument'])->whereNumber(['legalCase', 'document'])->scopeBindings()->name('documents.destroy');
    });
});

// ---------- Clients – superadmin (sab) + admin (apne assigned; admin ka banaya client usi ko assign) ----------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':superadmin,admin'])->prefix('clients')->name('api.clients.')->group(function () {
    Route::get('/', [ClientController::class, 'index'])->name('index');
    Route::get('options', [ClientController::class, 'options'])->name('options');
    Route::get('{client}', [ClientController::class, 'show'])->whereNumber('client')->name('show');
    Route::get('{client}/files/{type}', [ClientController::class, 'file'])->whereNumber('client')->whereIn('type', ['aadhaar', 'pan'])->name('file');
    Route::get('{client}/documents/{document}', [ClientController::class, 'document'])->whereNumber(['client', 'document'])->scopeBindings()->name('document');

    // add / edit / delete – superadmin + admin (admin ke liye active subscription zaroori)
    Route::middleware(SubscriptionMiddleware::class)->group(function () {
        Route::post('/', [ClientController::class, 'store'])->name('store');
        Route::post('{client}', [ClientController::class, 'update'])->whereNumber('client')->name('update');
        Route::delete('{client}', [ClientController::class, 'destroy'])->whereNumber('client')->name('destroy');
        Route::delete('{client}/documents/{document}', [ClientController::class, 'destroyDocument'])->whereNumber(['client', 'document'])->scopeBindings()->name('documents.destroy');
    });
});

// ---------- Admin (Advocate) ke apne Users – admin + superadmin ----------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':admin,superadmin'])->prefix('my-users')->name('api.my-users.')->group(function () {
    Route::get('options', [AdminUserController::class, 'options'])->name('options');
    Route::get('/', [AdminUserController::class, 'index'])->name('index');
    Route::get('{user}', [AdminUserController::class, 'show'])->whereNumber('user')->name('show');

    // admin ke paas ACTIVE SUBSCRIPTION hona zaroori (superadmin ko chhoot)
    Route::middleware(SubscriptionMiddleware::class)->group(function () {
        Route::post('/', [AdminUserController::class, 'store'])->name('store');
        Route::post('{user}', [AdminUserController::class, 'update'])->whereNumber('user')->name('update');
        Route::patch('{user}/status', [AdminUserController::class, 'updateStatus'])->whereNumber('user')->name('status');
        Route::delete('{user}', [AdminUserController::class, 'destroy'])->whereNumber('user')->name('destroy');
    });
});

// ---------- Admin – My Subscription (dekhna + superadmin se request) ----------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':admin'])->prefix('my-subscription')->name('api.my-subscription.')->group(function () {
    Route::get('/', [MySubscriptionController::class, 'show'])->name('show');
    Route::get('status', [MySubscriptionController::class, 'status'])->name('status');
    Route::post('request', [MySubscriptionController::class, 'request'])->middleware('throttle:5,1')->name('request');
});

// ---------- Payments – superadmin + admin, SIRF APNE (koi kisi ka nahi dekh sakta) ----------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':superadmin,admin'])->prefix('payments')->name('api.payments.')->group(function () {
    Route::get('options', [PaymentController::class, 'options'])->name('options');
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::post('/', [PaymentController::class, 'store'])->name('store');
    Route::get('{payment}', [PaymentController::class, 'show'])->whereNumber('payment')->name('show');
    Route::post('{payment}', [PaymentController::class, 'update'])->whereNumber('payment')->name('update');
    Route::delete('{payment}', [PaymentController::class, 'destroy'])->whereNumber('payment')->name('destroy');
    Route::get('{payment}/attachment', [PaymentController::class, 'attachment'])->whereNumber('payment')->name('attachment');
});

// ---------- Reports – superadmin (sab / kisi admin ki) + admin (sirf apni). Payments hamesha sirf apne ----------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':superadmin,admin'])
    ->get('reports', [ReportController::class, 'index'])->name('api.reports.index');