{{-- 1. On dit à ce fichier d'utiliser le menu bleu --}}
@extends('layouts.app')

@section('title', 'Tableau de Bord')

{{-- 2. On remplit la "place vide" du layout avec le contenu du dashboard --}}
@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 fw-bold text-dark">Résumé de l'activité</h1>
        <div class="badge bg-white text-dark shadow-sm p-2 border">
            <i class="bi bi-calendar3 me-2 text-primary"></i> {{ now()->format('d/m/Y') }}
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Carte Total Patients --}}
        <div class="col-md-6 col-xl-4">
            <div class="card card-stat shadow-sm p-4 bg-white">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary-subtle p-3 rounded-circle">
                        <i class="bi bi-people-fill text-primary fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h6 class="text-muted mb-1 text-uppercase small fw-bold">Total Patients</h6>
                        <span class="h2 fw-bold mb-0">{{ $stats['patients_count'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Carte Factures --}}
        <div class="col-md-6 col-xl-4">
            <div class="card card-stat shadow-sm p-4 bg-white" style="border-left-color: #198754;">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success-subtle p-3 rounded-circle">
                        <i class="bi bi-receipt-cutoff text-success fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h6 class="text-muted mb-1 text-uppercase small fw-bold">Factures (Aujourd'hui)</h6>
                        <span class="h2 fw-bold mb-0">{{ $stats['factures_today'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section Actions Rapides --}}
    <div class="card shadow-sm border-0 bg-white p-5 rounded-4">
        <h5 class="fw-bold mb-4"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Actions rapides</h5>
        <div class="d-flex flex-wrap gap-3">
            <button class="btn btn-primary btn-lg px-4 shadow-sm">
                <i class="bi bi-person-plus-fill me-2"></i> Enregistrer un nouveau patient
            </button>
            <button class="btn btn-outline-success btn-lg px-4">
                <i class="bi bi-cash-stack me-2"></i> Saisir une facture
            </button>
        </div>
    </div>
@endsection