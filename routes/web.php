<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\QuizAdminController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ScoreboardController;
use App\Http\Controllers\AdminScoreboardController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\PreferenceAdminController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login-submit', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login-submit');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register-submit', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register-submit');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Quiz Routes
    Route::get('/quiz', [QuizController::class, 'show'])->name('quiz.show');
    Route::post('/quiz/submit', [QuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/quiz/state', [QuizController::class, 'state'])->name('quiz.state');

    // Do You Prefer Routes
    Route::get('/preference', [PreferenceController::class, 'show'])->name('preference.show');
    Route::get('/preference/state', [PreferenceController::class, 'state'])->name('preference.state');
    Route::post('/preference/submit', [PreferenceController::class, 'submit'])->name('preference.submit');

    // Scoreboard
    Route::get('/scoreboard', [ScoreboardController::class, 'index'])->name('scoreboard.index');
    Route::get('/scoreboard/history', [ScoreboardController::class, 'history'])->name('scoreboard.history');

    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/admin/user/{id}', [AdminController::class, 'showUser'])->name('admin.user.show');
        Route::post('/admin/user/{id}/update', [AdminController::class, 'updateUser'])->name('admin.user.update');
        Route::delete('/admin/user/{id}/delete', [AdminController::class, 'deleteUser'])->name('admin.user.delete');
        Route::post('/admin/user/{id}/toggle-role', [AdminController::class, 'toggleRole'])->name('admin.user.toggle-role');

        // Quiz Admin Routes
        Route::get('/admin/quizzes', [QuizAdminController::class, 'index'])->name('admin.quizzes.index');
        Route::get('/admin/quizzes/create', [QuizAdminController::class, 'create'])->name('admin.quizzes.create');
        Route::post('/admin/quizzes', [QuizAdminController::class, 'store'])->name('admin.quizzes.store');
        Route::get('/admin/quizzes/{quiz}/edit', [QuizAdminController::class, 'edit'])->name('admin.quizzes.edit');
        Route::post('/admin/quizzes/{quiz}/update', [QuizAdminController::class, 'update'])->name('admin.quizzes.update');
        Route::post('/admin/quizzes/{quiz}/activate', [QuizAdminController::class, 'activate'])->name('admin.quizzes.activate');
        Route::post('/admin/quizzes/{quiz}/close', [QuizAdminController::class, 'close'])->name('admin.quizzes.close');
        Route::post('/admin/quizzes/{quiz}/reset', [QuizAdminController::class, 'reset'])->name('admin.quizzes.reset');
        Route::delete('/admin/quizzes/{quiz}', [QuizAdminController::class, 'destroy'])->name('admin.quizzes.destroy');
        Route::get('/admin/quizzes/{quiz}/results', [QuizAdminController::class, 'showResults'])->name('admin.quizzes.results');
        Route::get('/admin/quizzes/{quiz}/results/download', [QuizAdminController::class, 'downloadResults'])->name('admin.quizzes.results.download');
        Route::get('/admin/quizzes/{quiz}/live-count', [QuizAdminController::class, 'liveCount'])->name('admin.quizzes.live-count');

        // Scoreboard Admin Routes
        Route::get('/admin/scoreboard', [AdminScoreboardController::class, 'index'])->name('admin.scoreboard.index');
        Route::post('/admin/scoreboard', [AdminScoreboardController::class, 'store'])->name('admin.scoreboard.store');
        Route::delete('/admin/scoreboard/{entry}', [AdminScoreboardController::class, 'destroy'])->name('admin.scoreboard.destroy');

        // Do You Prefer Admin Routes
        Route::get('/admin/preference', [PreferenceAdminController::class, 'index'])->name('admin.preference.index');
        Route::get('/admin/preference/create', [PreferenceAdminController::class, 'create'])->name('admin.preference.create');
        Route::post('/admin/preference', [PreferenceAdminController::class, 'store'])->name('admin.preference.store');
        Route::get('/admin/preference/{game}/manage', [PreferenceAdminController::class, 'manage'])->name('admin.preference.manage');
        Route::post('/admin/preference/{game}/activate', [PreferenceAdminController::class, 'activate'])->name('admin.preference.activate');
        Route::post('/admin/preference/{game}/close', [PreferenceAdminController::class, 'closeGame'])->name('admin.preference.close');
        Route::post('/admin/preference/{game}/question/{question}/activate', [PreferenceAdminController::class, 'activateQuestion'])->name('admin.preference.question.activate');
        Route::post('/admin/preference/{game}/question/{question}/reveal', [PreferenceAdminController::class, 'revealAnswer'])->name('admin.preference.question.reveal');
        Route::post('/admin/preference/{game}/question/{question}/close', [PreferenceAdminController::class, 'closeQuestion'])->name('admin.preference.question.close');
        Route::post('/admin/preference/{game}/end-eliminatory', [PreferenceAdminController::class, 'endEliminatoryPhase'])->name('admin.preference.end-eliminatory');
        Route::delete('/admin/preference/{game}', [PreferenceAdminController::class, 'destroy'])->name('admin.preference.destroy');
        Route::get('/admin/preference/{game}/live-count', [PreferenceAdminController::class, 'liveCount'])->name('admin.preference.live-count');
    });
});
