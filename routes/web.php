<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\CircuitController;
use App\Http\Controllers\LitController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SalleController;

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

    // API Patient
    Route::get('/api/patients/{id}', [PatientController::class, 'apiShow'])->name('api.patients.show');

    // Mouvements (Circuits)
    Route::get('/circuits/create',                  [CircuitController::class, 'create'])->name('circuits.create');
    Route::get('/circuits/lits-disponibles',        [CircuitController::class, 'getLitsDisponibles'])->name('circuits.lits');
    Route::post('/mouvements/passage',              [CircuitController::class, 'storePassage'])->name('mouvements.passage');
    Route::post('/mouvements/admission',            [CircuitController::class, 'storeAdmission'])->name('mouvements.admission');
    Route::post('/mouvements/transfert',            [CircuitController::class, 'storeTransfert'])->name('mouvements.transfert');
    Route::post('/mouvements/sortie',               [CircuitController::class, 'storeSortie'])->name('mouvements.sortie');
    Route::get('/circuits/{circuit}/modifier',      [CircuitController::class, 'edit'])->name('circuits.edit');
    Route::put('/circuits/{circuit}',               [CircuitController::class, 'update'])->name('circuits.update');
    Route::delete('/circuits/{circuit}',            [CircuitController::class, 'destroy'])->name('circuits.destroy');

    // Lits (consultation pour tous)
    Route::get('/lits', [LitController::class, 'index'])->name('lits.index');

    // Transfert de lit (admin)
    Route::get('/lits/transfert', [LitController::class, 'transfertForm'])->name('lits.transfert.form');
    Route::post('/lits/transfert', [LitController::class, 'transfertLit'])->name('lits.transfert');

    // Administration
    Route::prefix('admin')->name('admin.')->group(function () {
        // Utilisateurs
        Route::get('/utilisateurs',                [UserController::class,'index'])        ->name('users.index');
        Route::post('/utilisateurs/{id}/approuver', [UserController::class,'approve'])     ->name('users.approve');
        Route::post('/utilisateurs/{id}/desapprouver', [UserController::class,'disapprove'])->name('users.disapprove');
        Route::post('/utilisateurs/{id}/role',     [UserController::class,'changerRole'])  ->name('users.role');
        Route::delete('/utilisateurs/{id}',        [UserController::class,'destroy'])      ->name('users.destroy');
        Route::get('/utilisateurs/{id}/transfer',  [UserController::class,'showTransferForm'])->name('users.transfer.form');
        Route::post('/utilisateurs/{id}/transfer', [UserController::class,'transferData'])->name('users.transfer');

        // Services
        Route::get('/services',                    [ServiceController::class,'index'])    ->name('services.index');
        Route::get('/services/nouveau',            [ServiceController::class,'create'])   ->name('services.create');
        Route::post('/services',                   [ServiceController::class,'store'])    ->name('services.store');
        Route::get('/services/{service}/modifier', [ServiceController::class,'edit'])     ->name('services.edit');
        Route::put('/services/{service}',          [ServiceController::class,'update'])   ->name('services.update');
        Route::delete('/services/{service}',       [ServiceController::class,'destroy'])  ->name('services.destroy');

        // Salles
        Route::get('/salles',                      [SalleController::class,'index'])->name('salles.index');
        Route::get('/salles/nouveau',              [SalleController::class,'create'])->name('salles.create');
        Route::post('/salles',                     [SalleController::class,'store'])->name('salles.store');
        Route::get('/salles/{salle}/modifier',     [SalleController::class,'edit'])->name('salles.edit');
        Route::put('/salles/{salle}',              [SalleController::class,'update'])->name('salles.update');
        Route::delete('/salles/{salle}',           [SalleController::class,'destroy'])->name('salles.destroy');

        // Lits (CRUD admin)
        Route::get('/lits/nouveau',                [LitController::class,'create'])->name('lits.create');
        Route::post('/lits',                       [LitController::class,'store'])->name('lits.store');
        Route::get('/lits/{lit}/modifier',         [LitController::class,'edit'])->name('lits.edit');
        Route::put('/lits/{lit}',                  [LitController::class,'update'])->name('lits.update');
        Route::delete('/lits/{lit}',               [LitController::class,'destroy'])->name('lits.destroy');
        Route::post('/lits/{lit}/statut',          [LitController::class,'changerStatut'])->name('lits.statut');
    });
});
