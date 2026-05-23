@extends('layouts.app')
@section('title', 'État des lits')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-hospital me-2"></i>État des lits en temps réel</h4>
    <div>
        <a href="{{ route('lits.index') }}" class="btn btn-outline-secondary btn-sm me-2">
            <i class="bi bi-arrow-repeat me-1"></i>Actualiser
        </a>
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('admin.lits.create') }}" class="btn btn-primary btn-sm me-2">
            <i class="bi bi-plus-circle me-1"></i>Ajouter un lit
        </a>
        <a href="{{ route('lits.transfert.form') }}" class="btn btn-warning btn-sm">
            <i class="bi bi-arrow-left-right me-1"></i>Transférer un lit
        </a>
        @endif
    </div>
</div>

<meta http-equiv="refresh" content="60">

{{-- Cartes statistiques --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--blue-l);color:var(--blue)">
                <i class="bi bi-hospital-fill"></i>
            </div>
            <div>
                <div class="stat-num" style="color:var(--blue)">{{ $stats['total'] ?? 0 }}</div>
                <div class="stat-lbl">Total lits</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#D1FAE5;color:#10B981">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <div class="stat-num" style="color:#10B981">{{ $stats['libres'] ?? 0 }}</div>
                <div class="stat-lbl">Lits libres</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#FEE2E2;color:#EF4444">
                <i class="bi bi-person-fill"></i>
            </div>
            <div>
                <div class="stat-num" style="color:#EF4444">{{ $stats['occupes'] ?? 0 }}</div>
                <div class="stat-lbl">Lits occupés</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#FFEDD5;color:#F97316">
                <i class="bi bi-tools"></i>
            </div>
            <div>
                <div class="stat-num" style="color:#F97316">{{ $stats['maintenance'] ?? 0 }}</div>
                <div class="stat-lbl">En maintenance</div>
            </div>
        </div>
    </div>
</div>

{{-- Affichage par service et salle --}}
@foreach($services as $service)
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="bi bi-building me-2"></i>
            {{ $service->nom_service }}
            <span class="badge bg-secondary ms-2">{{ $service->salles->sum(function($s) { return $s->lits->count(); }) }} lit(s)</span>
        </h5>
    </div>
    <div class="card-body">
        @foreach($service->salles as $salle)
        <div class="mb-4">
            <h6 class="fw-bold mb-2">
                <i class="bi bi-door-closed me-2"></i>
                {{ $salle->nom }}
                <small class="text-muted">(Capacité: {{ $salle->capacite }} | {{ $salle->lits->count() }} lit(s))</small>
            </h6>
            <div class="row g-2">
                @forelse($salle->lits as $lit)
                <div class="col-md-3 col-sm-4 col-6">
                    <div class="card lit-card h-100 {{ $lit->statut }}">
                        <div class="card-body p-3 text-center">
                            <h5 class="card-title mb-1">Lit N°{{ $lit->numero }}</h5>
                            <span class="badge
                                @if($lit->statut === 'libre') bg-success
                                @elseif($lit->statut === 'occupe') bg-danger
                                @elseif($lit->statut === 'maintenance') bg-warning text-dark
                                @else bg-secondary
                                @endif
                                mb-2">
                                @if($lit->statut === 'libre') Libre
                                @elseif($lit->statut === 'occupe') Occupé
                                @elseif($lit->statut === 'maintenance') Maintenance
                                @else Hors service
                                @endif
                            </span>
                            @if($lit->statut === 'occupe' && $lit->patient)
                            <div class="small text-muted mt-2">
                                <i class="bi bi-person me-1"></i>
                                {{ $lit->patient->prenom }} {{ $lit->patient->nom }}<br>
                                <small>Code: {{ $lit->patient->code_unique }}</small>
                            </div>
                            @endif
                            @if(Auth::user()->role === 'admin' && $lit->statut !== 'occupe')
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalStatut{{ $lit->id }}">
                                    <i class="bi bi-pencil me-1"></i>Changer statut
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-muted mb-0">Aucun lit dans cette salle.</p>
                </div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
</div>
@endforeach

{{-- Modals pour changer le statut (placés à la fin du body pour éviter les problèmes de positionnement) --}}
@foreach($services as $service)
    @foreach($service->salles as $salle)
        @foreach($salle->lits as $lit)
            @if(Auth::user()->role === 'admin' && $lit->statut !== 'occupe')
            <div class="modal fade" id="modalStatut{{ $lit->id }}" tabindex="-1" aria-labelledby="modalStatutLabel{{ $lit->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="{{ route('admin.lits.statut', $lit) }}" method="POST" id="formStatut{{ $lit->id }}">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalStatutLabel{{ $lit->id }}">
                                    <i class="bi bi-hospital me-2"></i>Changer le statut - Lit N°{{ $lit->numero }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nouveau statut</label>
                                    <select name="statut" class="form-select" required>
                                        <option value="libre" {{ $lit->statut === 'libre' ? 'selected' : '' }}>Libre</option>
                                        <option value="maintenance" {{ $lit->statut === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="hors_service" {{ $lit->statut === 'hors_service' ? 'selected' : '' }}>Hors service</option>
                                    </select>
                                </div>
                                <div class="alert alert-info small mb-0">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Le statut "Occupé" est géré automatiquement par le système.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle me-1"></i>Annuler
                                </button>
                                <button type="submit" class="btn btn-primary" id="btnSubmit{{ $lit->id }}">
                                    <i class="bi bi-check-circle me-1"></i>Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    @endforeach
@endforeach

@endsection

@section('scripts')
<script>
// Stabiliser les modals - éviter les problèmes de positionnement
document.addEventListener('DOMContentLoaded', function() {
    // Forcer Bootstrap à bien initialiser les modals
    var modalElements = document.querySelectorAll('.modal');
    modalElements.forEach(function(modalEl) {
        var modal = new bootstrap.Modal(modalEl, {
            backdrop: true,
            keyboard: true,
            focus: true
        });

        // Nettoyer le modal à la fermeture
        modalEl.addEventListener('hidden.bs.modal', function() {
            document.body.classList.remove('modal-open');
            var backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function(backdrop) {
                backdrop.remove();
            });
        });
    });
});

// Éviter la soumission multiple des formulaires
document.querySelectorAll('form[id^="formStatut"]').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        var submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Enregistrement...';
        }
    });
});
</script>

<style>
/* Styles pour les cartes lits */
.lit-card {
    transition: transform 0.2s ease;
}
.lit-card:hover {
    transform: translateY(-3px);
}
.lit-card.libre {
    border-left: 4px solid #10B981;
}
.lit-card.occupe {
    border-left: 4px solid #EF4444;
}
.lit-card.maintenance {
    border-left: 4px solid #F97316;
}
.lit-card.hors_service {
    border-left: 4px solid #6B7280;
    opacity: 0.7;
}

/* Stabiliser les modals */
.modal {
    background-color: rgba(0,0,0,0.5);
}
.modal-dialog {
    margin: 1.75rem auto;
}
.modal.fade .modal-dialog {
    transform: translate(0, -50px);
}
.modal.show .modal-dialog {
    transform: translate(0, 0);
}
</style>
@endsection
