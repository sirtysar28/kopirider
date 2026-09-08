<?php

use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\CalendarController as AdminCalendarController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarApiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadApiController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/check-date', [PageController::class, 'checkDate'])->name('check-date');
Route::get('/packages', [PageController::class, 'packages'])->name('packages');
Route::get('/free-events', [PageController::class, 'freeEvents'])->name('free-events');
Route::get('/gallery', [PageController::class, 'gallery'])->name('gallery');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms-and-conditions', [PageController::class, 'termsConditions'])->name('terms-and-conditions');

/*
|--------------------------------------------------------------------------
| Booking flow API (lead capture happens BEFORE the WhatsApp redirect)
|--------------------------------------------------------------------------
*/
Route::get('/api/calendar', [CalendarApiController::class, 'month'])->name('api.calendar');
Route::post('/api/leads', [LeadApiController::class, 'store'])->name('api.leads.store');
Route::post('/api/leads/whatsapp-opened', [LeadApiController::class, 'whatsappOpened'])->name('api.leads.whatsapp-opened');
Route::post('/api/analytics', [AnalyticsController::class, 'track'])->name('api.analytics.track');
Route::post('/api/midtrans/notification', [MidtransWebhookController::class, 'handle'])
    ->name('api.midtrans.notification');

/*
|--------------------------------------------------------------------------
| Admin authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');

    Route::get('/calendar', [AdminCalendarController::class, 'index'])->name('calendar.index');
    Route::post('/calendar', [AdminCalendarController::class, 'update'])->name('calendar.update');

    Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
    Route::get('/packages/create', [PackageController::class, 'create'])->name('packages.create');
    Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
    Route::get('/packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit');
    Route::put('/packages/{package}', [PackageController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');

    Route::post('/leads/{lead}/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::post('/payments/{payment}/generate-link', [PaymentController::class, 'generateLink'])->name('payments.generate-link');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
    Route::post('/gallery', [GalleryController::class, 'store'])->name('gallery.store');
    Route::post('/gallery/{media}/toggle', [GalleryController::class, 'toggle'])->name('gallery.toggle');
    Route::delete('/gallery/{media}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/branding', [SettingController::class, 'updateBranding'])->name('settings.branding');

    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');
});
