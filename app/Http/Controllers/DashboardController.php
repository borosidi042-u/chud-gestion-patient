<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Service;
use App\Models\Visite;
use App\Models\Lit;
use App\Models\Mouvement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_patients' => Patient::count(),
            'patients_aujourdhui' => Patient::whereDate('created_at', today())->count(),
            'total_services' => Service::where('is_active', true)->count(),
            'visites_en_cours' => Visite::where('statut', 'en_cours')->count(),
            'lits_libres' => Lit::where('statut', 'libre')->count(),
            'lits_occupes' => Lit::where('statut', 'occupe')->count(),
            'lits_maintenance' => Lit::where('statut', 'maintenance')->count(),
            'lits_hors_service' => Lit::where('statut', 'hors_service')->count(),
            'total_lits' => Lit::count(),
            'user_nom' => $user->prenom . ' ' . $user->nom,
            'user_role' => $user->role === 'admin' ? 'Administrateur' : ($user->role === 'infirmier' ? 'Infirmier' : 'Agent d\'accueil'),
            'user_email' => $user->email,
            'user_avatar' => strtoupper(substr($user->prenom,0,1) . substr($user->nom,0,1)),
        ];

        $derniersPatients = Patient::with('user')->latest()->take(5)->get();

        $derniersMouvements = Mouvement::with(['patient', 'service', 'agent'])
            ->latest('heure_arrivee')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'derniersPatients', 'derniersMouvements'));
    }
}
