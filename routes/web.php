<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
// AJOUTER CETTE LIGNE ICI :
use App\Http\Controllers\Admin\UserController; 
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
    return view('welcome');
});

// --- ROUTES PUBLIQUES ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [LoginController::class, 'register']);


// --- ROUTES PROTÉGÉES ---
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Gestion des utilisateurs (Admin)
    Route::get('/admin/utilisateurs', [UserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/utilisateurs/{id}/role', [UserController::class, 'changerRole'])->name('admin.users.role');
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});