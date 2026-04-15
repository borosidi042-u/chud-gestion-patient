<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient; // Assurez-vous d'avoir ce modèle plus tard

class DashboardController extends Controller
{
    public function index()
    {
        // On crée un tableau de stats fictives pour que la vue ne plante pas
        $stats = [
            'patients_count' =>0, // Vous remplacerez par Patient::count() plus tard
            'factures_today' => 0,
        ];

        return view('dashboard', compact('stats'));
    }
}