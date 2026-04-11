<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController; // Importation du contrôleur

Route::get('/', function () {
    return view('welcome');
});
// Route pour la page d'accueil (le Tableau de Bord)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Les futures routes pour les patients (on les prépare)
// Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
