<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\HelpCenterController;

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
use App\Http\Controllers\User\ProgressController;
use App\Http\Controllers\User\ChatbotController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Models\User;

// ==========================================
// ROOT REDIRECT (SMART ROLE BASED)
// ==========================================
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('welcome');
    }

    return Auth::user()->role == 1
        ? redirect()->route('admin.dashboard')
        : redirect()->route('welcome');
});

// ==========================================
// PUBLIC WELCOME ROUTE
// ==========================================
Route::get('/welcome', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/help-center', [HelpCenterController::class, 'index'])->name('help.center');


Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');



// ==========================================
// USER ROUTES (ROLE = 0) 
// ==========================================
Route::middleware(['auth', 'user'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [WelcomeController::class, 'index'])->name('dashboard');

        // Levels
        Route::get('/levels', [UserLevelController::class, 'index'])->name('levels.index');
        Route::get('/levels/{level}', [UserLevelController::class, 'show'])->name('levels.show');
        Route::post('/levels/{level}/select', [UserLevelController::class, 'select'])->name('levels.select');
        Route::get('/levels/{level}/next', [UserLevelController::class, 'nextLevel'])->name('levels.next');
        Route::post('/levels/{level}/check-and-select', [UserLevelController::class, 'checkAndSelect'])
            ->name('levels.check-and-select');

        Route::get('/skills', [UserSkillController::class, 'index'])->name('skills.index');
        Route::get('/skills/{skill}', [UserSkillController::class, 'show'])->name('skills.show');

        // Level selection routes
        Route::get('/skills/{skill}/select-level', [UserSkillController::class, 'selectLevel'])->name('skills.select-level');
        Route::post('/skills/{skill}/start-practice', [UserSkillController::class, 'startPractice'])->name('skills.start-practice');
        Route::post('/skills/{skill}/videos/{video}/track', [UserSkillController::class, 'trackVideoProgress'])
            ->name('skills.videos.track')
            ->middleware('auth');

        // Practice routes
        Route::get('/skills/{skill}/practice', [UserSkillController::class, 'practice'])->name('skills.practice');
        Route::post('/skills/{skill}/practice/submit', [UserSkillController::class, 'submitPractice'])->name('skills.practice.submit');

        Route::get('/skills/{skill}/results', [UserSkillController::class, 'results'])
            ->name('skills.results');

        // Progress tracking routes
        Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');

        // Chatbot route
        Route::post('/chatbot/send', [ChatbotController::class, 'sendMessage'])->name('chatbot.send');
        Route::get('/chatbot/history', [ChatbotController::class, 'getHistory'])->name('chatbot.history');


        // AJAX routes
        Route::get('/skills/{skill}/levels', [UserSkillController::class, 'getLevelsForSkill'])->name('skills.levels');
        Route::post('/update-level', [UserSkillController::class, 'updateLevel'])->name('update-level');
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

        // Users - Reorganized for better order
        Route::prefix('users')->name('users.')->group(function () {
            // Custom routes that need to come BEFORE resource routes
            Route::get('export', [AdminUserController::class, 'export'])->name('export');
            Route::post('bulk-delete', [AdminUserController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('bulk-update-role', [AdminUserController::class, 'bulkUpdateRole'])->name('bulk-update-role');

            // Resource routes (these will handle index, create, store, show, edit, update, destroy)
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::get('/create', [AdminUserController::class, 'create'])->name('create');
            Route::post('/', [AdminUserController::class, 'store'])->name('store');
            Route::get('/{user}', [AdminUserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
            Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');

            // Additional user-specific routes (should come after the resource routes)
            Route::get('/{user}/progress', [AdminUserController::class, 'progress'])->name('progress');
            Route::post('/{user}/toggle-role', [AdminUserController::class, 'toggleRole'])->name('toggle-role');
        });

        // Skills
        Route::resource('skills', AdminSkillController::class);
        Route::post('skills/reorder', [AdminSkillController::class, 'reorder'])->name('skills.reorder');


        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/levels', [App\Http\Controllers\Admin\QuestionController::class, 'getAllLevels']);
            Route::get('/skills/{skill}/levels', [App\Http\Controllers\Admin\QuestionController::class, 'getLevelsBySkill']);
            Route::get('/videos/all', [App\Http\Controllers\Admin\QuestionController::class, 'getAllVideos']);
            Route::get('/videos', [App\Http\Controllers\Admin\QuestionController::class, 'getVideosBySkill']);
            Route::get('/video/{videoId}', [App\Http\Controllers\Admin\QuestionController::class, 'getVideoById']);
        });
    });

// ==========================================
// AUTH ROUTES
// ==========================================
require __DIR__ . '/auth.php';
