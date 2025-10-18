<?php

use App\Http\Controllers\AdditionalAgreementController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\MahallaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\ParserController;
use App\Http\Controllers\PaymentScheduleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root based on authentication
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('monitoring.report1');
    }
    return redirect()->route('login');
});

// Guest routes (not authenticated)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Mahalla API routes (for AJAX)
    Route::get('/mahallas/{tumanId}', [MahallaController::class, 'getByTuman']);
    Route::post('/mahallas', [MahallaController::class, 'store']);

    Route::resource('contracts', ContractController::class);
    Route::post('/contracts/{contract}/generate-schedule', [ContractController::class, 'generateSchedule'])
        ->name('contracts.generate-schedule');

    Route::post('/contracts/{contract}/add-schedule-item', [ContractController::class, 'addScheduleItem'])
        ->name('contracts.add-schedule-item');

    // Additional Agreements
    Route::get('/contracts/{contract}/additional-agreements/create', [AdditionalAgreementController::class, 'create'])
        ->name('additional-agreements.create');
    Route::post('/contracts/{contract}/additional-agreements', [AdditionalAgreementController::class, 'store'])
        ->name('additional-agreements.store');
    Route::get('/additional-agreements/{agreement}', [AdditionalAgreementController::class, 'show'])
        ->name('additional-agreements.show');
    Route::delete('/additional-agreements/{agreement}', [AdditionalAgreementController::class, 'destroy'])
        ->name('additional-agreements.destroy');

    // Payment recording
    Route::post('/payment-schedules/{schedule}/record-payment', [ContractController::class, 'recordPayment'])
        ->name('payment-schedules.record-payment');

    Route::put('/payment-schedules/{schedule}', [PaymentScheduleController::class, 'update'])
        ->name('payment-schedules.update');

    // Payment Schedules
    Route::delete('/payment-schedules/{schedule}', [PaymentScheduleController::class, 'destroy'])
        ->name('payment-schedules.destroy');

    // Distributions
    Route::get('/distributions/create', [DistributionController::class, 'create'])
        ->name('distributions.create');
    Route::post('/distributions', [DistributionController::class, 'store'])
        ->name('distributions.store');
    Route::get('/distributions/{distribution}/edit', [DistributionController::class, 'edit'])
        ->name('distributions.edit');
    Route::put('/distributions/{distribution}', [DistributionController::class, 'update'])
        ->name('distributions.update');
    Route::delete('/distributions/{distribution}', [DistributionController::class, 'destroy'])
        ->name('distributions.destroy');

    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Lots Management
    // Export route must be before resource routes to avoid conflicts
    Route::get('/lots/export', [LotController::class, 'export'])->name('lots.export');

    // Resource routes
    Route::resource('lots', LotController::class);
    Route::post('/lots/{lot}/toggle-like', [LotController::class, 'toggleLike'])->name('lots.toggleLike');
    Route::post('/lots/{lot}/send-message', [LotController::class, 'sendMessage'])
        ->name('lots.sendMessage');

    // Admin routes for viewing analytics
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/lot-views/{lot}', [AnalyticsController::class, 'lotViews'])
            ->name('lot.views');
        Route::get('/lot-messages/{lot}', [AnalyticsController::class, 'lotMessages'])
            ->name('lot.messages');
        Route::get('/login-history', [AnalyticsController::class, 'loginHistory'])
            ->name('login.history');

        Route::post('/messages/{message}/mark-read', [AnalyticsController::class, 'markMessageAsRead'])
            ->name('messages.mark-read');
        Route::post('/messages/{message}/mark-replied', [AnalyticsController::class, 'markMessageAsReplied'])
            ->name('messages.mark-replied');
    });

    // Monitoring Reports
    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('monitoring.report1');
        })->name('index');

        Route::get('/report1', [MonitoringController::class, 'report1'])->name('report1');
        Route::get('/report1/details', [MonitoringController::class, 'report1Details'])->name('report1.details');

        // Report 2
        Route::get('/report2', [MonitoringController::class, 'report2'])->name('report2');
        Route::get('/report2/details', [MonitoringController::class, 'report2Details'])->name('report2.details');

        // Report 3
        Route::get('/report3', [MonitoringController::class, 'report3'])->name('report3');
        Route::get('/report3/details', [MonitoringController::class, 'report3Details'])->name('report3.details');

        Route::get('/monitoring/payment-schedule/{lotId}', [MonitoringController::class, 'paymentSchedule'])
            ->name('monitoring.payment-schedule');
    });
});

//testparserstart
Route::get('/parser', [ParserController::class, 'index'])->name('parser.index');
Route::get('/parser/parse-lots', [ParserController::class, 'parseLots'])->name('parser.parse');
Route::get('/parser/parse-single', [ParserController::class, 'parseSingleLot'])->name('parser.single');
