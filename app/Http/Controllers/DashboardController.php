<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Facture;
use App\Models\Circuit;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_patients'  => Patient::count(),
            'total_factures'  => Facture::count(),
            'total_circuits'  => Circuit::count(),
            'total_services'  => Service::count(),
            'montant_total'   => Facture::sum('montant'),
            'patients_aujourdhui' => Patient::whereDate('created_at', today())->count(),
        ];

        // 5 derniers patients enregistrés
        $derniersPatients = Patient::with('user')->latest()->take(5)->get();

        // 5 derniers passages (circuit)
        $derniersCircuits = Circuit::with(['patient', 'service'])->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'derniersPatients', 'derniersCircuits'));
    }
}
