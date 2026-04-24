<?php

use App\Http\Controllers\EscalationController;
use App\Http\Controllers\FeedbackAdminController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FeedbackManualController;
use App\Http\Controllers\FeedbackReportController;
use App\Http\Controllers\HodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public Routes - CCBRT Hospital Feedback System
Route::get('/', function () {
    return view('home');
})->name('home');

Route::post('/locale', function (Request $request) {
    $validated = $request->validate([
        'locale' => ['required', 'in:en,sw'],
    ]);

    session(['locale' => $validated['locale']]);

    return back();
})->name('locale.switch');

// Feedback Routes
Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
Route::get('/feedback/confirmation/{reference}', [FeedbackController::class, 'confirmation'])->name('feedback.confirmation');

// Track Feedback Routes
Route::get('/track', [FeedbackController::class, 'trackForm'])->name('feedback.track');
Route::post('/track', [FeedbackController::class, 'track'])->name('feedback.track.submit');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Feedback Admin Routes
    Route::get('/admin/feedback', [FeedbackAdminController::class, 'index'])->name('feedback.admin.index');
    Route::get('/admin/feedback/{feedback}', [FeedbackAdminController::class, 'show'])->name('feedback.admin.show');
    Route::post('/admin/feedback/{feedback}/status', [FeedbackAdminController::class, 'updateStatus'])->name('feedback.admin.status');
    Route::post('/admin/feedback/{feedback}/assignment', [FeedbackAdminController::class, 'updateAssignment'])->name('feedback.admin.assignment');
    Route::post('/admin/feedback/{feedback}/notes', [FeedbackAdminController::class, 'storeNote'])->name('feedback.admin.note');
    Route::post('/admin/feedback/{feedback}/responses', [FeedbackAdminController::class, 'storeResponse'])->name('feedback.admin.response');

    // Manual Feedback Entry Routes
    Route::get('/admin/feedback/manual/create', [FeedbackManualController::class, 'create'])->name('feedback.manual.create');
    Route::post('/admin/feedback/manual', [FeedbackManualController::class, 'store'])->name('feedback.manual.store');

    // Feedback Report Routes
    Route::get('/reports/feedback', [FeedbackReportController::class, 'index'])->name('reports.feedback.index');
    Route::get('/reports/feedback/export', [FeedbackReportController::class, 'export'])->name('reports.feedback.export');

    // User Management Routes
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/pending', [UserManagementController::class, 'pending'])->name('users.pending');
    Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/approve', [UserManagementController::class, 'approve'])->name('users.approve');
    Route::post('/users/{user}/deactivate', [UserManagementController::class, 'deactivate'])->name('users.deactivate');
    Route::post('/users/{user}/activate', [UserManagementController::class, 'activate'])->name('users.activate');
    Route::post('/users/{user}/role', [UserManagementController::class, 'changeRole'])->name('users.role');

    // HOD / Incharge Management
    Route::resource('hods', HodController::class)->except(['show']);

    // Escalation Admin Routes
    Route::get('/escalations', [EscalationController::class, 'index'])->name('escalations.index');
    Route::post('/admin/feedback/{feedback}/escalate', [EscalationController::class, 'store'])->name('escalations.store');
});

// Public HOD Response Routes (token-based, no login)
Route::get('/escalations/respond/{token}', [EscalationController::class, 'respond'])->name('escalations.respond');
Route::post('/escalations/respond/{token}', [EscalationController::class, 'submitResponse'])->name('escalations.submit');
Route::get('/escalations/done/{token}', [EscalationController::class, 'done'])->name('escalations.done');

require __DIR__.'/auth.php';
