<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;


Route::get('/',[HomeController::class,'IndexPage']);

Route::get('/post/{id}',[HomeController::class,'PostPage']);

// NEW: AJAX endpoint for email validation
Route::post('/check-email-availability',[HomeController::class,'checkEmailAvailability']);

Route::post('/event-registration',[HomeController::class,'EventRegistration']);

// Enhanced Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\EnhancedDashboardController::class, 'enhancedDashboard'])->name('dashboard');
    Route::get('/dashboard/analytics', [App\Http\Controllers\EnhancedDashboardController::class, 'getDashboardAnalytics'])->name('dashboard.analytics');
    Route::get('/dashboard/recent-activities', [App\Http\Controllers\EnhancedDashboardController::class, 'getRecentActivities'])->name('dashboard.recent-activities');
    Route::post('/dashboard/compare-events', [App\Http\Controllers\EnhancedDashboardController::class, 'compareEvents'])->name('dashboard.compare-events');
});
  
require_once __DIR__ . '/ddd_api.php';

// NEW: User Management routes (following your pattern)
require_once __DIR__ . '/user_management.php';
