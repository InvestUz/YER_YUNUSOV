<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\MonitoringController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root based on authentication
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
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

    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Lots Management
    // Export route must be before resource routes to avoid conflicts
    Route::get('/lots/export', [LotController::class, 'export'])->name('lots.export');

    // AJAX route for mahallas
    Route::get('/mahallas/by-tuman', [LotController::class, 'getMahallas'])->name('mahallas.by-tuman');

    // Resource routes
    Route::resource('lots', LotController::class);

    // Monitoring Reports
    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/', [MonitoringController::class, 'index'])->name('index');
        Route::get('/report1', [MonitoringController::class, 'report1'])->name('report1');
        Route::get('/report2', [MonitoringController::class, 'report2'])->name('report2');
        Route::get('/report3', [MonitoringController::class, 'report3'])->name('report3');
    });
});
