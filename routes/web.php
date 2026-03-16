<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\AnswerController;
use App\Http\Controllers\Admin\ChatbotRuleController;
use App\Http\Controllers\Admin\ChatbotSessionController;
use App\Http\Controllers\Admin\UserController;

// ==========================================
// ROOT REDIRECT
// ==========================================
Route::get('/', function () {
    if (Auth::check()) {

        $user = Auth::user();

        // role 0 = normal user
        if ($user->role === '0') {
            return redirect()->route('welcome');
        }

        // role 1 = admin
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('welcome');
});


// ==========================================
// PUBLIC WELCOME
// ==========================================
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');


// ==========================================
// PROFILE ROUTES
// ==========================================
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});


// ==========================================
// ADMIN ROUTES
// ==========================================
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Levels
        Route::resource('levels', LevelController::class);

        // Videos
        Route::resource('videos', VideoController::class);
        Route::post('videos/bulk-delete', [VideoController::class, 'bulkDelete'])->name('videos.bulk-delete');
        Route::post('videos/update-order', [VideoController::class, 'updateOrder'])->name('videos.update-order');
        Route::get('videos/statistics', [VideoController::class, 'statistics'])->name('videos.statistics');
        Route::get('levels/{level}/videos', [VideoController::class, 'levelVideos'])->name('levels.videos');


        // Questions and Answers
        Route::resource('questions', QuestionController::class);
        Route::resource('videos', VideoController::class);
        Route::get('/api/videos', [App\Http\Controllers\Admin\VideoApiController::class, 'index'])->name('api.videos');


        Route::get('/api/skills/{skillId}/levels', [App\Http\Controllers\Admin\ApiController::class, 'getSkillLevels'])->name('api.skills.levels');
        Route::get('/api/videos', [App\Http\Controllers\Admin\VideoApiController::class, 'index'])->name('api.videos');

        // Chatbot Rules
        Route::resource('chatbot/rules', ChatbotRuleController::class)->names([
            'index' => 'chatbot.rules.index',
            'create' => 'chatbot.rules.create',
            'store' => 'chatbot.rules.store',
            'show' => 'chatbot.rules.show',
            'edit' => 'chatbot.rules.edit',
            'update' => 'chatbot.rules.update',
            'destroy' => 'chatbot.rules.destroy',
        ]);

        // Chatbot Sessions
        Route::resource('chatbot/sessions', ChatbotSessionController::class)->names([
            'index' => 'chatbot.sessions.index',
            'show' => 'chatbot.sessions.show',
        ])->only(['index', 'show']);

        // Chatbot Analytics
        Route::get('chatbot/analytics', [ChatbotSessionController::class, 'analytics'])->name('chatbot.analytics');

        Route::resource('users', UserController::class);
        Route::post('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
        Route::post('users/bulk-update-role', [UserController::class, 'bulkUpdateRole'])->name('users.bulk-update-role');
        Route::post('users/{user}/toggle-role', [UserController::class, 'toggleRole'])->name('users.toggle-role');
        Route::post('users/{user}/verify-email', [UserController::class, 'verifyEmail'])->name('users.verify-email');
        Route::get('users/{user}/progress', [UserController::class, 'progress'])->name('users.progress');
        Route::get('users/export/csv', [UserController::class, 'export'])->name('users.export');
        Route::get('users/statistics/data', [UserController::class, 'statistics'])->name('users.statistics');

        Route::prefix('levels/{level}')->name('levels.')->group(function () {
            Route::get('videos', [VideoController::class, 'levelVideos'])->name('videos');
            Route::get('questions', [QuestionController::class, 'levelQuestions'])->name('questions');
        });

        //skills
        Route::resource('skills', App\Http\Controllers\Admin\SkillController::class);
        Route::post('skills/reorder', [App\Http\Controllers\Admin\SkillController::class, 'reorder'])->name('skills.reorder');
        Route::get('skills/{skill}/levels', [App\Http\Controllers\Admin\SkillController::class, 'getLevels'])->name('skills.levels');
    });


require __DIR__ . '/auth.php';
