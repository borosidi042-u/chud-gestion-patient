<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Facture;
use App\Models\Service;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord avec les statistiques globales.
     */
    public function index()
    {
        // 1. On récupère le nombre total de patients enregistrés
        $totalPatients = Patient::count();

        // 2. On récupère le nombre de factures enregistrées aujourd'hui uniquement
        $facturesToday = Facture::whereDate('created_at', today())->count();

        // 3. Optionnel : On peut aussi compter le nombre de services disponibles
        $totalServices = Service::count();

        // On prépare un tableau pour regrouper ces statistiques
        $stats = [
            'patients_count' => $totalPatients,
            'factures_today' => $facturesToday,
            'services_count' => $totalServices,
        ];

        // On envoie les données à la vue 'dashboard.blade.php'
        return view('dashboard', compact('stats'));
    }
}