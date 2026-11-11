<?php

use Illuminate\Support\Facades\Route;

// Sabhi pages static hain – koi controller nahi
Route::view('/', 'layouts.home')->name('home');
Route::view('/about', 'layouts.about')->name('about');
Route::view('/practice-areas', 'layouts.practice-index')->name('practice.index');
Route::view('/career', 'layouts.career')->name('career');
Route::view('/contact', 'layouts.contact')->name('contact');

// Auth pages (form submit API se hota hai – /api/auth/login, /api/auth/register)
Route::view('/login', 'layouts.login')->name('login');
Route::view('/register', 'layouts.register')->name('register');        // Advocate (admin) registration
Route::view('/signup', 'layouts.signup')->name('signup');              // User registration
Route::view('/dashboard', 'user.dashboard')->name('user.dashboard');   // resources/views/user/dashboard.blade.php (role = user)

// ================= ADMIN (ADVOCATE) DASHBOARD =================
Route::view('/admin', 'admin.dashboard')->name('admin.dashboard');   // resources/views/admin/dashboard.blade.php
Route::view('/payments', 'frontlayout.payments')->name('payments');
Route::view('/reports', 'frontlayout.reports')->name('reports');                                            // superadmin (sab) + admin (apni)                                         // superadmin + admin – apne payments
Route::view('/admin/subscription', 'admin.subscription')->name('admin.subscription');                       // admin ka subscription (accept)
Route::view('/admin/users', 'admin.users.index')->name('admin.users');                                      // admin ke users (list)
Route::view('/admin/users/create', 'admin.users.form')->name('admin.users.create');                         // naya user
Route::view('/admin/users/{id}/edit', 'admin.users.form')->whereNumber('id')->name('admin.users.edit');     // edit user

// Alias: agar kahin route('frontlayout.dashboard') likha ho to bhi superadmin dashboard khule
Route::redirect('/frontlayout/dashboard', '/superadmin')->name('frontlayout.dashboard');

// ================= SUPERADMIN PANEL (static, views/frontlayout) =================
Route::prefix('superadmin')->name('superadmin.')->group(function () {
    Route::view('/', 'frontlayout.dashboard')->name('dashboard');   // sidebar wala superadmin dashboard
    // Cases (dynamic – resources/views/frontlayout/cases/)
    Route::view('/cases', 'frontlayout.cases.index')->name('cases');
    Route::view('/cases/create', 'frontlayout.cases.form')->name('cases.create');
    Route::view('/cases/{id}', 'frontlayout.cases.show')->whereNumber('id')->name('cases.show');
    Route::view('/cases/{id}/edit', 'frontlayout.cases.form')->whereNumber('id')->name('cases.edit');
    Route::view('/clients', 'frontlayout.clients')->name('clients');
    Route::view('/enquiries', 'frontlayout.enquiries')->name('enquiries');
    Route::view('/advocates', 'frontlayout.advocates')->name('advocates');
    Route::view('/settings', 'frontlayout.settings')->name('settings');

    // Admin (Advocate) management – data API se aata hai
    Route::view('/admins', 'frontlayout.admins')->name('admins');
    Route::view('/admins/create', 'frontlayout.admin-create')->name('admins.create');
    Route::view('/admins/{id}/edit', 'frontlayout.admin-create')->whereNumber('id')->name('admins.edit');

    // Users + Login activity (API se)
    Route::view('/users', 'frontlayout.users')->name('users');
    Route::view('/login-activity', 'frontlayout.login-activity')->name('login-activity');

    // Subscriptions (resources/views/frontlayout/subscriptions/)
    Route::view('/subscriptions/plans', 'frontlayout.subscriptions.plans')->name('subscriptions.plans');
    Route::view('/subscriptions', 'frontlayout.subscriptions.index')->name('subscriptions');

    // Career (resources/views/frontlayout/career/)
    Route::view('/career/jobs', 'frontlayout.career.jobs')->name('career.jobs');
    Route::view('/career/jobs/create', 'frontlayout.career.job-form')->name('career.jobs.create');
    Route::view('/career/jobs/{id}/edit', 'frontlayout.career.job-form')->whereNumber('id')->name('career.jobs.edit');
    Route::view('/career/applications', 'frontlayout.career.applications')->name('career.applications');

    // Practice Areas (dynamic – data API se)
    Route::view('/practice-areas', 'frontlayout.practice-areas')->name('practice-areas');
    Route::view('/practice-areas/create', 'frontlayout.practice-area-create')->name('practice-areas.create');
    Route::view('/practice-areas/{id}/edit', 'frontlayout.practice-area-create')->whereNumber('id')->name('practice-areas.edit');
});

// Practice area detail pages: /practice-areas/criminal, /practice-areas/challan, ... (slugs config/practice.php se)
Route::get('/practice-areas/{slug}', function (string $slug) {
    abort_unless(array_key_exists($slug, config('practice', [])), 404);
    return view('layouts.practice-show', ['slug' => $slug]);
})->name('practice.show');