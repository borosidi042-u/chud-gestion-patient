<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\CircuitController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ServiceController;

Route::get('/', fn() => redirect()->route('login'));

// ── Authentification ─────────────────────────────────────────────────────
Route::get('/login',    [LoginController::class,'showLoginForm'])->name('login');
Route::post('/login',   [LoginController::class,'login']);
Route::get('/register', [LoginController::class,'showRegisterForm'])->name('register');
Route::post('/register',[LoginController::class,'register']);

// ── Mot de passe oublié — 3 étapes ──────────────────────────────────────
Route::get('/mot-de-passe-oublie',  [PasswordResetController::class,'showEmailForm'])->name('password.request');
Route::post('/mot-de-passe-oublie', [PasswordResetController::class,'sendCode'])->name('password.send-code');
Route::get('/verification-code',    [PasswordResetController::class,'showCodeForm'])->name('password.verify-code');
Route::post('/verification-code',   [PasswordResetController::class,'verifyCode']);
Route::post('/renvoyer-code',       [PasswordResetController::class,'resendCode'])->name('password.resend-code');
Route::get('/nouveau-mot-de-passe', [PasswordResetController::class,'showNewPasswordForm'])->name('password.new-password');
Route::post('/nouveau-mot-de-passe',[PasswordResetController::class,'updatePassword'])->name('password.update-new');

// ── Routes protégées ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
    Route::post('/logout',   [LoginController::class,'logout'])->name('logout');

    // Profil
    Route::get('/profil',                  [ProfileController::class,'index'])->name('profile.index');
    Route::put('/profil/informations',     [ProfileController::class,'updateInfo'])->name('profile.update-info');
    Route::put('/profil/mot-de-passe',     [ProfileController::class,'updatePassword'])->name('profile.update-password');

    // Patients
    Route::get('/patients',                    [PatientController::class,'index']) ->name('patients.index');
    Route::get('/patients/nouveau',            [PatientController::class,'create'])->name('patients.create');
    Route::post('/patients',                   [PatientController::class,'store']) ->name('patients.store');
    Route::get('/patients/{patient}',          [PatientController::class,'show'])  ->name('patients.show');
    Route::get('/patients/{patient}/modifier', [PatientController::class,'edit'])  ->name('patients.edit');
    Route::put('/patients/{patient}',          [PatientController::class,'update'])->name('patients.update');
    Route::delete('/patients/{patient}',       [PatientController::class,'destroy'])->name('patients.destroy');

    // Factures
    Route::get('/factures',                    [FactureController::class,'index']) ->name('factures.index');
    Route::get('/factures/nouvelle',           [FactureController::class,'create'])->name('factures.create');
    Route::post('/factures',                   [FactureController::class,'store']) ->name('factures.store');
    Route::get('/factures/{facture}/modifier', [FactureController::class,'edit'])  ->name('factures.edit');
    Route::put('/factures/{facture}',          [FactureController::class,'update'])->name('factures.update');
    Route::delete('/factures/{facture}',       [FactureController::class,'destroy'])->name('factures.destroy');
    
    Route::get('/factures/{facture}/preview',  [FactureController::class, 'preview'])->name('factures.preview');
    Route::get('/factures/{facture}/download', [FactureController::class, 'download'])->name('factures.download');

    // Circuits
    Route::get('/circuits/nouveau',      [CircuitController::class,'create']) ->name('circuits.create');
    Route::post('/circuits',             [CircuitController::class,'store'])  ->name('circuits.store');
    Route::delete('/circuits/{circuit}', [CircuitController::class,'destroy'])->name('circuits.destroy');

    // ── Administration (vérification manuelle du rôle admin) ─────────────────
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // Gestion des utilisateurs - avec vérification manuelle dans le contrôleur
        Route::get('/utilisateurs',                [UserController::class,'index'])        ->name('users.index');
        Route::post('/utilisateurs/{id}/approuver', [UserController::class,'approve'])     ->name('users.approve');
        Route::post('/utilisateurs/{id}/desapprouver', [UserController::class,'disapprove'])->name('users.disapprove');
        Route::post('/utilisateurs/{id}/role',     [UserController::class,'changerRole'])  ->name('users.role');
        Route::delete('/utilisateurs/{id}',        [UserController::class,'destroy'])      ->name('users.destroy');
        
        // Gestion des services
        Route::get('/services',                    [ServiceController::class,'index'])    ->name('services.index');
        Route::get('/services/nouveau',            [ServiceController::class,'create'])   ->name('services.create');
        Route::post('/services',                   [ServiceController::class,'store'])    ->name('services.store');
        Route::get('/services/{service}/modifier', [ServiceController::class,'edit'])     ->name('services.edit');
        Route::put('/services/{service}',          [ServiceController::class,'update'])   ->name('services.update');
        Route::delete('/services/{service}',       [ServiceController::class,'destroy'])  ->name('services.destroy');
    });
});