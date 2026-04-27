@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('content')

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="animation-delay:.05s">
            <div class="stat-icon" style="background:#E8F3FC;color:#005A9C"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="stat-num" style="color:#005A9C">{{ $stats['total_patients'] }}</div>
                <div class="stat-lbl">Total patients</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="animation-delay:.1s">
            <div class="stat-icon" style="background:#E3F5EF;color:#00875A"><i class="bi bi-receipt-cutoff"></i></div>
            <div>
                <div class="stat-num" style="color:#00875A">{{ $stats['total_factures'] }}</div>
                <div class="stat-lbl">Total factures</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="animation-delay:.15s">
            <div class="stat-icon" style="background:#FFF3E0;color:#D97706"><i class="bi bi-person-plus-fill"></i></div>
            <div>
                <div class="stat-num" style="color:#D97706">{{ $stats['patients_aujourdhui'] }}</div>
                <div class="stat-lbl">Patients aujourd'hui</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="animation-delay:.2s">
            <div class="stat-icon" style="background:#F3F0FF;color:#6D28D9"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="stat-num" style="color:#6D28D9;font-size:1.3rem">{{ number_format($stats['montant_total'],0,',',' ') }}</div>
                <div class="stat-lbl">FCFA total facturé</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-person-plus-fill me-2" style="color:var(--blue)"></i>Derniers patients enregistrés</span>
                <a href="{{ route('patients.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body p-0">
                @forelse($derniersPatients as $p)
                <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:1px solid #F0F4F8">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:38px;height:38px;border-radius:50%;background:var(--blue-l);color:var(--blue);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;flex-shrink:0">
                            {{ strtoupper(substr($p->prenom,0,1).substr($p->nom,0,1)) }}
                        </div>
                        <div>
                            <a href="{{ route('patients.show',$p) }}" class="fw-600 text-decoration-none" style="color:var(--text);font-weight:600">{{ $p->prenom }} {{ $p->nom }}</a>
                            <div><span class="code-badge">{{ $p->code_unique }}</span></div>
                        </div>
                    </div>
                    <small style="color:var(--muted)">{{ $p->created_at->diffForHumans() }}</small>
                </div>
                @empty
                <div class="text-center py-5" style="color:var(--muted)"><i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>Aucun patient encore</div>
                @endforelse
            </div>
            <div class="card-footer text-center py-3">
                <a href="{{ route('patients.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-person-plus me-1"></i>Nouveau patient</a>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-diagram-3-fill me-2" style="color:var(--green)"></i>Derniers passages</span>
                <a href="{{ route('circuits.create') }}" class="btn btn-sm btn-outline-primary">Ajouter</a>
            </div>
            <div class="card-body p-0">
                @forelse($derniersCircuits as $c)
                <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:1px solid #F0F4F8">
                    <div>
                        <a href="{{ route('patients.show',$c->patient_id) }}" class="fw-600 text-decoration-none" style="color:var(--text);font-weight:600">{{ $c->patient->prenom }} {{ $c->patient->nom }}</a>
                        <div style="font-size:.8rem;color:var(--muted)"><i class="bi bi-building me-1"></i>{{ $c->service->nom_service ?? '—' }}</div>
                    </div>
                    <small style="color:var(--muted)">{{ $c->created_at->diffForHumans() }}</small>
                </div>
                @empty
                <div class="text-center py-5" style="color:var(--muted)"><i class="bi bi-diagram-3 fs-1 d-block mb-2 opacity-25"></i>Aucun passage enregistré</div>
                @endforelse
            </div>
            <div class="card-footer text-center py-3">
                <a href="{{ route('circuits.create') }}" class="btn btn-success btn-sm"><i class="bi bi-diagram-3 me-1"></i>Enregistrer un passage</a>
            </div>
        </div>
    </div>
</div>
@endsection
