<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LevelController as AdminLevelController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\ChatbotRuleController as AdminChatbotRuleController;
use App\Http\Controllers\Admin\ChatbotSessionController as AdminChatbotSessionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SkillController as AdminSkillController;

// User Controllers
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\LevelController as UserLevelController;
use App\Http\Controllers\User\SkillController as UserSkillController;

// ==========================================
// ROOT REDIRECT (SMART ROLE BASED)
// ==========================================
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('welcome');
    }

    return Auth::user()->role == 1
        ? redirect()->route('admin.dashboard')
        : redirect()->route('user.dashboard');
});

// ==========================================
// PUBLIC WELCOME ROUTE
// ==========================================
Route::get('/welcome', [WelcomeController::class, 'index'])->name('welcome');

// ==========================================
// USER ROUTES (ROLE = USER) - ALL PROTECTED
// ==========================================
Route::middleware(['auth', 'user'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

        // Levels
        Route::get('/levels', [UserLevelController::class, 'index'])->name('levels.index');
        Route::get('/levels/{level}', [UserLevelController::class, 'show'])->name('levels.show');
        Route::post('/levels/{level}/select', [UserLevelController::class, 'select'])->name('levels.select');
        Route::get('/levels/{level}/next', [UserLevelController::class, 'nextLevel'])->name('levels.next');

        // Skills
        Route::get('/skills', [UserSkillController::class, 'index'])->name('skills.index');
        Route::get('/skills/{skill}', [UserSkillController::class, 'show'])->name('skills.show');
        Route::get('/skills/{skill}/practice', [UserSkillController::class, 'practice'])->name('skills.practice');
        Route::post('/skills/{skill}/practice/submit', [UserSkillController::class, 'submitPractice'])->name('skills.practice.submit');
        Route::get('/skills/{skill}/video/{video}', [UserSkillController::class, 'watchVideo'])->name('skills.video');
    });

// ==========================================
// PROFILE ROUTES (AUTHENTICATED USERS)
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// ADMIN ROUTES (ROLE = ADMIN)
// ==========================================
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Levels
        Route::resource('levels', AdminLevelController::class);

        // Videos
        Route::resource('videos', AdminVideoController::class);
        Route::post('videos/bulk-delete', [AdminVideoController::class, 'bulkDelete'])->name('videos.bulk-delete');
        Route::post('videos/update-order', [AdminVideoController::class, 'updateOrder'])->name('videos.update-order');

        // Questions
        Route::resource('questions', AdminQuestionController::class);

        // Chatbot
        Route::prefix('chatbot')->name('chatbot.')->group(function () {

            // Rules
            Route::resource('rules', AdminChatbotRuleController::class);

            // Sessions
            Route::resource('sessions', AdminChatbotSessionController::class)
                ->only(['index', 'show']);

            // Analytics
            Route::get('analytics', [AdminChatbotSessionController::class, 'analytics'])
                ->name('analytics');
        });

        // Users
        Route::resource('users', AdminUserController::class);
        Route::post('users/bulk-delete', [AdminUserController::class, 'bulkDelete'])->name('users.bulk-delete');
        Route::post('users/{user}/toggle-role', [AdminUserController::class, 'toggleRole'])->name('users.toggle-role');
        Route::get('users/{user}/progress', [AdminUserController::class, 'progress'])
            ->name('users.progress');
        Route::post('users/bulk-update-role', [AdminUserController::class, 'bulkUpdateRole'])
            ->name('users.bulk-update-role');
        Route::get('users/export', [AdminUserController::class, 'export'])
    ->name('users.export');

        // Skills
        Route::resource('skills', AdminSkillController::class);
        Route::post('skills/reorder', [AdminSkillController::class, 'reorder'])->name('skills.reorder');
    });

// ==========================================
// AUTH ROUTES
// ==========================================
require __DIR__ . '/auth.php';
