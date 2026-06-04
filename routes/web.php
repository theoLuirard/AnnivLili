<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\QuizAdminController;
use App\Http\Controllers\QuizController;

Route::get('/', function () {
    return view('welcome');
});

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

    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/admin/user/{id}', [AdminController::class, 'showUser'])->name('admin.user.show');
        Route::post('/admin/user/{id}/update', [AdminController::class, 'updateUser'])->name('admin.user.update');
        Route::delete('/admin/user/{id}/delete', [AdminController::class, 'deleteUser'])->name('admin.user.delete');

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
    });
});
