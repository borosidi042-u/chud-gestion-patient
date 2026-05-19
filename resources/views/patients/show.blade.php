@extends('layouts.app')
@section('title', 'Dossier — ' . $patient->prenom . ' ' . $patient->nom)
@section('content')

{{-- En-tête patient --}}
<div class="card mb-4 animate__animated animate__fadeIn">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex gap-3 align-items-center">
                <div style="width:52px;height:52px;border-radius:14px;background:var(--blue-l);color:var(--blue);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;flex-shrink:0">
                    {{ strtoupper(substr($patient->prenom,0,1).substr($patient->nom,0,1)) }}
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="font-size:1.1rem">{{ $patient->prenom }} {{ $patient->nom }}</h4>
                    <div class="d-flex flex-wrap gap-3" style="font-size:.82rem;color:var(--muted)">
                        <span><i class="bi bi-upc me-1"></i><span class="code-badge">{{ $patient->code_unique }}</span></span>
                        @if($patient->telephone)<span><i class="bi bi-telephone me-1"></i>{{ $patient->telephone }}</span>@endif
                        @if($patient->npi)<span><i class="bi bi-person-badge me-1"></i>NPI: {{ $patient->npi }}</span>@endif
                        @if($patient->date_naissance)<span><i class="bi bi-calendar3 me-1"></i>{{ $patient->date_naissance->format('d/m/Y') }}</span>@endif
                        @if($patient->adresse)<span><i class="bi bi-geo-alt me-1"></i>{{ $patient->adresse }}</span>@endif
                    </div>
                    <div style="font-size:.75rem;color:#bbb;margin-top:3px">
                        Enregistré par {{ $patient->user->prenom ?? '' }} {{ $patient->user->nom ?? '' }} le {{ $patient->created_at->format('d/m/Y à H:i') }}
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-pencil me-1"></i>Modifier
                </a>
                @if($visiteEnCours)
                    <a href="{{ route('circuits.create', ['patient_id' => $patient->id, 'tab' => 'passage']) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-arrow-right me-1"></i>Passage
                    </a>
                    <a href="{{ route('circuits.create', ['patient_id' => $patient->id, 'tab' => 'admission']) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-hospital me-1"></i>Admission
                    </a>
                    <a href="{{ route('circuits.create', ['patient_id' => $patient->id, 'tab' => 'transfert']) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-arrow-left-right me-1"></i>Transfert
                    </a>
                    <a href="{{ route('circuits.create', ['patient_id' => $patient->id, 'tab' => 'sortie']) }}" class="btn btn-sm btn-danger" id="btnTerminerVisite">
                        <i class="bi bi-door-closed me-1"></i>Sortie
                    </a>
                @else
                    <a href="{{ route('circuits.create', ['patient_id' => $patient->id, 'tab' => 'admission']) }}" class="btn btn-sm btn-success">
                        <i class="bi bi-hospital-fill me-1"></i>Nouvelle admission
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Bandeau visite en cours --}}
@if($visiteEnCours)
<div class="alert alert-success mb-4 animate__animated animate__pulse">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <i class="bi bi-check-circle-fill me-2 fs-4"></i>
            <strong>Visite N°{{ $visiteEnCours->numero_visite }} en cours</strong>
            <small class="d-block">Début: {{ $visiteEnCours->date_debut->format('d/m/Y à H:i') }} | Durée: {{ $visiteEnCours->getDuree() }}</small>
        </div>
        <div>
            @php $litActuel = $visiteEnCours->getLitActuel(); @endphp
            @if($litActuel)
            <span class="badge bg-info">
                <i class="bi bi-hospital me-1"></i>Lit N°{{ $litActuel->numero }}
            </span>
            @endif
        </div>
    </div>
</div>
@endif

{{-- Parcours patient (flux chronologique horizontal par visite) --}}
<div class="card animate__animated animate__fadeInUp">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Parcours patient (flux chronologique)</h5>
        <small class="text-muted">Cliquez droit sur un service pour voir les actions</small>
    </div>
    <div class="card-body">
        @forelse($visites as $visite)
        <div class="visite-section mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <h6 class="mb-0">
                    <i class="bi bi-calendar-heart me-2"></i>
                    Visite N°{{ $visite->numero_visite }}
                    @if($visite->statut === 'terminee')
                    <span class="badge bg-success ms-2">TERMINÉE</span>
                    @else
                    <span class="badge bg-primary ms-2">EN COURS</span>
                    @endif
                </h6>
                <div class="text-muted small">
                    <i class="bi bi-clock me-1"></i>{{ $visite->date_debut->format('d/m/Y à H:i') }}
                    @if($visite->date_fin)
                    → {{ $visite->date_fin->format('d/m/Y à H:i') }}
                    @endif
                    <span class="ms-2 badge bg-secondary">{{ $visite->getDuree() }}</span>
                </div>
            </div>

            {{-- Timeline horizontale des services --}}
            <div class="timeline-horizontal">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    @php $mouvements = $visite->getTimeline(); @endphp
                    @foreach($mouvements as $index => $mouvement)
                        @if($mouvement->type !== 'sortie')
                        <div class="service-card {{ $loop->last && $visite->statut === 'en_cours' ? 'active' : 'completed' }} mouvement-item"
                             data-mouvement-id="{{ $mouvement->id }}"
                             data-mouvement-type="{{ $mouvement->type }}"
                             data-mouvement-service-id="{{ $mouvement->service_id }}"
                             data-mouvement-service="{{ $mouvement->service->nom_service ?? 'Service' }}"
                             data-mouvement-date="{{ $mouvement->heure_arrivee->format('d/m/Y à H:i') }}"
                             data-mouvement-note="{{ $mouvement->note ?: 'Aucune note' }}"
                             data-mouvement-agent="{{ $mouvement->agent->prenom ?? '' }} {{ $mouvement->agent->nom ?? '' }}"
                             data-mouvement-lit="{{ $mouvement->lit ? $mouvement->lit->numero : 'Aucun' }}"
                             data-mouvement-salle="{{ $mouvement->salle ? $mouvement->salle->nom : 'Aucune' }}">
                            <div class="service-card-body">
                                <div class="service-number">{{ $index + 1 }}</div>
                                <div class="service-name">{{ $mouvement->service->nom_service ?? 'Service' }}</div>
                                <div class="service-type">
                                    @if($mouvement->type === 'entree')
                                        <span class="badge bg-info">Admission</span>
                                    @elseif($mouvement->type === 'transfert')
                                        <span class="badge bg-warning text-dark">Transfert</span>
                                    @elseif($mouvement->type === 'passage')
                                        <span class="badge bg-secondary">Passage sans lit</span>
                                    @endif
                                </div>
                                <div class="service-status">
                                    @if($loop->last && $visite->statut === 'en_cours')
                                        <span class="badge bg-primary">EN COURS</span>
                                    @else
                                        <span class="badge bg-success">TERMINÉ</span>
                                    @endif
                                </div>
                                <div class="service-time">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $mouvement->heure_arrivee->format('H:i') }}
                                </div>
                                @if($mouvement->lit)
                                <div class="service-location mt-1">
                                    <i class="bi bi-hospital me-1"></i>
                                    <small>Lit N°{{ $mouvement->lit->numero }}</small>
                                </div>
                                @endif
                                @if($mouvement->salle)
                                <div class="service-location">
                                    <i class="bi bi-door-open me-1"></i>
                                    <small>Salle: {{ $mouvement->salle->nom }}</small>
                                </div>
                                @endif
                            </div>
                        </div>

                        @if(!$loop->last && $mouvements[$index + 1]->type !== 'sortie')
                        <div class="timeline-arrow">
                            <i class="bi bi-arrow-right fs-3 text-muted"></i>
                        </div>
                        @endif
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Bilan de sortie --}}
            @php
                $sortieMouvement = $mouvements->where('type', 'sortie')->first();
            @endphp
            @if($sortieMouvement)
            <div class="sortie-bilan mt-3 p-3 bg-light rounded">
                <div class="d-flex align-items-center gap-3">
                    <div class="sortie-icon">
                        <i class="bi bi-door-closed-fill fs-2 text-danger"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 text-danger">SORTIE DE L'HÔPITAL</h6>
                        <div class="small text-muted">
                            <i class="bi bi-clock me-1"></i>Date de sortie: {{ $sortieMouvement->heure_arrivee->format('d/m/Y à H:i') }}
                            @if($sortieMouvement->note)
                            <br><i class="bi bi-chat-text me-1"></i>Note: {{ $sortieMouvement->note }}
                            @endif
                            <br><i class="bi bi-person me-1"></i>Validé par: {{ $sortieMouvement->agent->prenom ?? '' }} {{ $sortieMouvement->agent->nom ?? '' }}
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($visite->validated_by && $visite->validatedBy)
            <div class="mt-3 pt-2 border-top">
                <small class="text-muted">
                    <i class="bi bi-check-circle me-1"></i>
                    Visite terminée par {{ $visite->validatedBy->prenom }} {{ $visite->validatedBy->nom }}
                    le {{ $visite->date_fin ? $visite->date_fin->format('d/m/Y à H:i') : '' }}
                </small>
            </div>
            @endif
        </div>
        @empty
        <div class="text-center py-5" style="color:var(--muted)">
            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
            Aucun historique pour ce patient.
            <a href="{{ route('circuits.create', ['patient_id' => $patient->id, 'tab' => 'admission']) }}" class="d-block mt-2">
                Démarrer une nouvelle admission
            </a>
        </div>
        @endforelse
    </div>
</div>

{{-- Modal pour afficher les détails du mouvement --}}
<div class="modal fade" id="modalMouvementDetail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails du mouvement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm">
                    <tr><th width="35%">Service</th><td id="detailService"></td></tr>
                    <tr><th>Type</th><td id="detailType"></td></tr>
                    <tr><th>Date et heure</th><td id="detailDate"></td></tr>
                    <tr><th>Salle</th><td id="detailSalle"></td></tr>
                    <tr><th>Lit</th><td id="detailLit"></td></tr>
                    <tr><th>Agent</th><td id="detailAgent"></td></tr>
                    <tr><th>Note</th><td id="detailNote"></td></tr>
                </table>
            </div>
            <div class="modal-footer" id="modalActions">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<style>
/* Timeline horizontale */
.timeline-horizontal {
    overflow-x: auto;
    padding-bottom: 10px;
}

.service-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
    cursor: context-menu;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.service-card.completed {
    border-left: 4px solid #10B981;
}

.service-card.active {
    border-left: 4px solid #3B82F6;
    background: #EFF6FF;
}

.service-card-body {
    padding: 12px 16px;
    text-align: center;
    position: relative;
}

.service-number {
    position: absolute;
    top: -10px;
    left: -10px;
    width: 24px;
    height: 24px;
    background: var(--blue);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.service-name {
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 6px;
    color: var(--text);
}

.service-type {
    margin-bottom: 6px;
}

.service-status {
    margin-bottom: 6px;
}

.service-time {
    font-size: 0.7rem;
    color: var(--muted);
}

.service-location {
    font-size: 0.65rem;
    color: var(--muted);
}

.timeline-arrow {
    display: flex;
    align-items: center;
    justify-content: center;
}

.sortie-bilan {
    animation: fadeIn 0.5s ease;
    border-left: 4px solid #EF4444;
}

.sortie-icon {
    width: 50px;
    height: 50px;
    background: #FEE2E2;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Menu contextuel personnalisé */
.context-menu {
    position: fixed;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    z-index: 10000;
    min-width: 200px;
    overflow: hidden;
    animation: fadeIn 0.2s ease;
    border: 1px solid #e5e7eb;
}

.context-menu-item {
    padding: 10px 16px;
    cursor: pointer;
    transition: background 0.2s;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.85rem;
}

.context-menu-item i {
    width: 18px;
    font-size: 1rem;
}

.context-menu-item:hover {
    background: var(--blue-l);
}

.context-menu-item.danger {
    color: var(--red);
}

.context-menu-item.danger:hover {
    background: #FEE2E2;
}

.context-menu-divider {
    height: 1px;
    background: #e5e7eb;
    margin: 4px 0;
}

/* Responsive */
@media (max-width: 768px) {
    .service-card {
        min-width: 160px !important;
    }
    .service-name {
        font-size: 0.8rem;
    }
}
</style>

<script>
// Confirmation pour terminer la visite
document.getElementById('btnTerminerVisite')?.addEventListener('click', function(e) {
    if (!confirm('Êtes-vous sûr de vouloir terminer cette visite ? Cette action clôturera la visite en cours.')) {
        e.preventDefault();
    }
});

// Menu contextuel personnalisé
let contextMenu = null;

function closeContextMenu() {
    if (contextMenu) {
        contextMenu.remove();
        contextMenu = null;
    }
}

function showContextMenu(event, mouvement) {
    event.preventDefault();
    closeContextMenu();

    const isAdmin = {{ Auth::user()->role === 'admin' ? 'true' : 'false' }};

    contextMenu = document.createElement('div');
    contextMenu.className = 'context-menu';
    contextMenu.style.left = event.pageX + 'px';
    contextMenu.style.top = event.pageY + 'px';

    // Détail (visible pour tous)
    const detailItem = document.createElement('div');
    detailItem.className = 'context-menu-item';
    detailItem.innerHTML = '<i class="bi bi-eye"></i> Voir les détails';
    detailItem.onclick = function() {
        closeContextMenu();
        document.getElementById('detailService').textContent = mouvement.service;

        let typeLabel = '';
        if (mouvement.type === 'entree') typeLabel = 'Admission';
        else if (mouvement.type === 'transfert') typeLabel = 'Transfert';
        else if (mouvement.type === 'passage') typeLabel = 'Passage sans lit';
        else typeLabel = mouvement.type;
        document.getElementById('detailType').textContent = typeLabel;

        document.getElementById('detailDate').textContent = mouvement.date;
        document.getElementById('detailSalle').textContent = mouvement.salle;
        document.getElementById('detailLit').textContent = mouvement.lit;
        document.getElementById('detailAgent').textContent = mouvement.agent;
        document.getElementById('detailNote').textContent = mouvement.note;

        const modalFooter = document.getElementById('modalActions');
        modalFooter.innerHTML = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>';

        new bootstrap.Modal(document.getElementById('modalMouvementDetail')).show();
    };
    contextMenu.appendChild(detailItem);

    // Modifier (uniquement pour admin)
    if (isAdmin) {
        const editItem = document.createElement('div');
        editItem.className = 'context-menu-item';
        editItem.innerHTML = '<i class="bi bi-pencil"></i> Modifier';
        editItem.onclick = function() {
            closeContextMenu();
            window.location.href = '/circuits/' + mouvement.id + '/modifier';
        };
        contextMenu.appendChild(editItem);

        // Supprimer (uniquement pour admin)
        const deleteItem = document.createElement('div');
        deleteItem.className = 'context-menu-item danger';
        deleteItem.innerHTML = '<i class="bi bi-trash"></i> Supprimer';
        deleteItem.onclick = function() {
            closeContextMenu();
            if (confirm('Supprimer définitivement ce mouvement ?')) {
                fetch('/circuits/' + mouvement.id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Erreur lors de la suppression');
                    }
                });
            }
        };
        contextMenu.appendChild(deleteItem);
    }

    document.body.appendChild(contextMenu);

    // Fermer le menu en cliquant ailleurs
    setTimeout(() => {
        document.addEventListener('click', closeContextMenu);
        document.addEventListener('contextmenu', closeContextMenu);
    }, 0);
}

// Attacher l'événement contextmenu à chaque mouvement
document.querySelectorAll('.mouvement-item').forEach(el => {
    el.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const mouvement = {
            id: this.dataset.mouvementId,
            service: this.dataset.mouvementService,
            serviceId: this.dataset.mouvementServiceId,
            type: this.dataset.mouvementType,
            date: this.dataset.mouvementDate,
            note: this.dataset.mouvementNote,
            agent: this.dataset.mouvementAgent,
            lit: this.dataset.mouvementLit,
            salle: this.dataset.mouvementSalle
        };
        showContextMenu(e, mouvement);
    });
});

// Empêcher le menu contextuel du navigateur sur les éléments
document.querySelectorAll('.service-card').forEach(el => {
    el.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        return false;
    });
});
</script>
@endsection
