@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('content')

<div class="container-fluid">
    {{-- Carte de bienvenue avec animation --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card animate__animated animate__fadeInDown">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Bonjour, {{ $stats['user_nom'] }} !</h4>
                        <p class="mb-0 text-muted">
                            <i class="bi bi-person-badge me-1"></i>
                            @if(Auth::user()->role === 'admin')
                                Administrateur
                            @elseif(Auth::user()->role === 'infirmier')
                                Infirmier
                            @else
                                Agent d'accueil
                            @endif
                            <span class="mx-2">•</span>
                            <i class="bi bi-envelope me-1"></i>{{ $stats['user_email'] }}
                        </p>
                    </div>
                    <div class="user-avatar">
                        {{ $stats['user_avatar'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ligne 1 : Patients et services --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0s;">
                <div class="stat-icon" style="background:var(--blue-l);color:var(--blue)">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="stat-num" style="color:var(--blue)">{{ $stats['total_patients'] }}</div>
                    <div class="stat-lbl">Total patients</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                <div class="stat-icon" style="background:#E8F3FC;color:#005A9C">
                    <i class="bi bi-calendar-plus"></i>
                </div>
                <div>
                    <div class="stat-num" style="color:#005A9C">{{ $stats['patients_aujourdhui'] }}</div>
                    <div class="stat-lbl">Patients aujourd'hui</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <div class="stat-icon" style="background:#D1FAE5;color:#10B981">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <div class="stat-num" style="color:#10B981">{{ $stats['total_services'] }}</div>
                    <div class="stat-lbl">Services actifs</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                <div class="stat-icon" style="background:#FEF3C7;color:#D97706">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div>
                    <div class="stat-num" style="color:#D97706">{{ $stats['visites_en_cours'] }}</div>
                    <div class="stat-lbl">Visites en cours</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ligne 2 : État des lits --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                <div class="stat-icon" style="background:#E0E7FF;color:#4F46E5">
                    <i class="bi bi-hospital-fill"></i>
                </div>
                <div>
                    <div class="stat-num" style="color:#4F46E5">{{ $stats['total_lits'] }}</div>
                    <div class="stat-lbl">Total lits</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.5s;">
                <div class="stat-icon" style="background:#D1FAE5;color:#10B981">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="stat-num" style="color:#10B981">{{ $stats['lits_libres'] }}</div>
                    <div class="stat-lbl">Lits libres</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.6s;">
                <div class="stat-icon" style="background:#FEE2E2;color:#EF4444">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div>
                    <div class="stat-num" style="color:#EF4444">{{ $stats['lits_occupes'] }}</div>
                    <div class="stat-lbl">Lits occupés</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.7s;">
                <div class="stat-icon" style="background:#FFEDD5;color:#F97316">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <div class="stat-num" style="color:#F97316">{{ $stats['lits_maintenance'] }}</div>
                    <div class="stat-lbl">En maintenance</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Derniers patients enregistrés --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100 animate__animated animate__fadeInLeft">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Derniers patients enregistrés</h5>
                </div>
                <div class="card-body p-0">
                    @if($derniersPatients->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Nom complet</th>
                                    <th>Enregistré le</th>
                                    <th>Par</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($derniersPatients as $patient)
                                <tr>
                                    <td><span class="code-badge">{{ $patient->code_unique }}</span></td>
                                    <td>
                                        <a href="{{ route('patients.show', $patient) }}" class="text-decoration-none">
                                            {{ $patient->prenom }} {{ $patient->nom }}
                                        </a>
                                    </td>
                                    <td><small>{{ $patient->created_at->format('d/m/Y à H:i') }}</small></td>
                                    <td><small>{{ $patient->user->prenom ?? '' }} {{ $patient->user->nom ?? '' }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                        Aucun patient enregistré.
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-white text-end">
                    <a href="{{ route('patients.index') }}" class="btn btn-sm btn-outline-primary">
                        Voir tous <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Derniers mouvements --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100 animate__animated animate__fadeInRight">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Derniers mouvements</h5>
                </div>
                <div class="card-body p-0">
                    @if($derniersMouvements->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Type</th>
                                    <th>Service</th>
                                    <th>Heure</th>
                                    <th>Agent</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($derniersMouvements as $mouvement)
                                <tr>
                                    <td>
                                        <a href="{{ route('patients.show', $mouvement->patient_id) }}" class="text-decoration-none">
                                            <span class="code-badge">{{ $mouvement->patient->code_unique ?? 'N/A' }}</span>
                                        </a>
                                    </td>
                                    <td>
                                        @if($mouvement->type === 'entree')
                                            <span class="badge bg-success">Admission</span>
                                        @elseif($mouvement->type === 'sortie')
                                            <span class="badge bg-danger">Sortie</span>
                                        @elseif($mouvement->type === 'transfert')
                                            <span class="badge bg-warning text-dark">Transfert</span>
                                        @else
                                            <span class="badge bg-info">Passage</span>
                                        @endif
                                    </td>
                                    <td>{{ $mouvement->service->nom_service ?? '—' }}</td>
                                    <td><small>{{ $mouvement->heure_arrivee->format('d/m/Y H:i') }}</small></td>
                                    <td><small>{{ $mouvement->agent->prenom ?? '' }} {{ $mouvement->agent->nom ?? '' }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                        Aucun mouvement enregistré.
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-white text-end">
                    <a href="{{ route('circuits.create') }}" class="btn btn-sm btn-outline-primary">
                        Nouveau mouvement <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<style>
.welcome-card {
    background: linear-gradient(135deg, #005A9C 0%, #003F6E 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.user-avatar {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
}

.stat-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
</style>

<!-- Animate.css pour les animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
@endsection
