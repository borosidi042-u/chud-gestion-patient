@extends('layouts.app')
@section('title', 'Dossier — ' . $patient->prenom . ' ' . $patient->nom)
@section('content')

{{-- ── En-tête patient ── --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex gap-3 align-items-center">
                <div style="width:52px;height:52px;border-radius:14px;background:var(--blue-l);color:var(--blue);
                    display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;flex-shrink:0">
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
                        Enregistré par {{ $patient->user->prenom ?? '' }} {{ $patient->user->nom ?? '' }}
                        le {{ $patient->created_at->format('d/m/Y à H:i') }}
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('patients.edit',$patient) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-pencil me-1"></i>Modifier
                </a>
                <a href="{{ route('factures.create',['patient_id'=>$patient->id]) }}" class="btn btn-sm btn-success">
                    <i class="bi bi-receipt me-1"></i>Ajouter facture
                </a>
                <a href="{{ route('circuits.create',['patient_id'=>$patient->id]) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-diagram-3 me-1"></i>Enregistrer passage
                </a>
                <form method="POST" action="{{ route('patients.destroy',$patient) }}"
                      onsubmit="return confirm('Supprimer définitivement ce patient ?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash me-1"></i>Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ── Statistiques ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--blue-l);color:var(--blue)"><i class="bi bi-calendar2-week-fill"></i></div>
            <div><div class="stat-num" style="color:var(--blue)">{{ $stats['nb_visites'] }}</div><div class="stat-lbl">Visites</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="animation-delay:.05s">
            <div class="stat-icon" style="background:#FFF3E0;color:#D97706"><i class="bi bi-diagram-3-fill"></i></div>
            <div><div class="stat-num" style="color:#D97706">{{ $stats['nb_passages'] }}</div><div class="stat-lbl">Passages</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="animation-delay:.1s">
            <div class="stat-icon" style="background:var(--green-l);color:var(--green)"><i class="bi bi-receipt-cutoff"></i></div>
            <div><div class="stat-num" style="color:var(--green)">{{ $stats['nb_factures'] }}</div><div class="stat-lbl">Factures</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="animation-delay:.15s">
            <div class="stat-icon" style="background:#F3F0FF;color:#6D28D9"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="stat-num" style="color:#6D28D9;font-size:1.1rem">{{ number_format($stats['total_montant'],0,',',' ') }}</div>
                <div class="stat-lbl">FCFA facturé</div>
            </div>
        </div>
    </div>
</div>

{{-- ── ONGLETS ── --}}
<ul class="nav nav-pills mb-3 gap-2" id="dossierTabs">
    <li class="nav-item">
        <button class="nav-link active" onclick="showTab('visites')" id="tab-visites"
                style="border-radius:9px;font-size:.875rem;font-weight:500">
            <i class="bi bi-calendar2-week me-1"></i>Visites médicales
            <span class="badge ms-1" style="background:var(--blue-l);color:var(--blue)">{{ $visites->count() }}</span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" onclick="showTab('factures')" id="tab-factures"
                style="border-radius:9px;font-size:.875rem;font-weight:500">
            <i class="bi bi-receipt-cutoff me-1"></i>Factures
            <span class="badge ms-1" style="background:var(--green-l);color:var(--green)">{{ $factures->count() }}</span>
        </button>
    </li>
</ul>

{{-- ════════════════════════════════════════════════════════════════════════
     ONGLET 1 — VISITES MÉDICALES (regroupées par visite_id)
════════════════════════════════════════════════════════════════════════ --}}
<div id="pane-visites">

@if($visites->isEmpty())
<div class="card">
    <div class="card-body text-center py-5" style="color:var(--muted)">
        <i class="bi bi-calendar2-x fs-1 d-block mb-2 opacity-25"></i>
        Aucune visite enregistrée.
        <a href="{{ route('circuits.create',['patient_id'=>$patient->id]) }}" class="d-block mt-2">
            Enregistrer la première visite
        </a>
    </div>
</div>
@else

@foreach($visites as $visite)
@php
    $entree  = $visite['entree'];
    $sortie  = $visite['sortie'];
    $passages= $visite['passages'];
    $valide  = $visite['valide'];
    $visiteId= $visite['visite_id'];
@endphp

<div class="card mb-3" style="border-left: 4px solid {{ $valide ? 'var(--green)' : ($sortie ? '#D97706' : 'var(--blue)') }}">
    {{-- En-tête visite --}}
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            {{-- Icône statut --}}
            @if($valide)
                <div style="width:36px;height:36px;border-radius:50%;background:var(--green-l);
                    color:var(--green);display:flex;align-items:center;justify-content:center;font-size:1rem">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
            @elseif($sortie)
                <div style="width:36px;height:36px;border-radius:50%;background:#FFF3E0;
                    color:#D97706;display:flex;align-items:center;justify-content:center;font-size:1rem">
                    <i class="bi bi-check-circle"></i>
                </div>
            @else
                <div style="width:36px;height:36px;border-radius:50%;background:var(--blue-l);
                    color:var(--blue);display:flex;align-items:center;justify-content:center;font-size:1rem">
                    <i class="bi bi-activity"></i>
                </div>
            @endif

            <div>
                <div class="fw-semibold" style="font-size:.9rem">
                    Visite du {{ $visite['date_debut']->format('d/m/Y') }}
                    @if($entree && $entree->type_entree)
                        <span class="badge ms-1" style="
                            background:{{ $entree->type_entree === 'urgence' ? 'var(--red-l)' : ($entree->type_entree === 'hospitalisation' ? '#F3F0FF' : 'var(--blue-l)') }};
                            color:{{ $entree->type_entree === 'urgence' ? 'var(--red)' : ($entree->type_entree === 'hospitalisation' ? '#6D28D9' : 'var(--blue)') }};
                            font-size:.7rem">
                            {{ $entree->type_entree === 'urgence' ? '🚨 Urgence' : ($entree->type_entree === 'hospitalisation' ? '🏥 Hospitalisation' : '🩺 Consultation') }}
                        </span>
                    @endif
                </div>
                <div style="font-size:.76rem;color:var(--muted)">
                    {{ $passages->count() }} passage(s) — {{ $visite['nb_services'] }} service(s)
                    @if($visite['duree'])
                        — Durée : <strong>{{ $visite['duree'] }}</strong>
                    @elseif(!$sortie)
                        — <span style="color:var(--blue)">En cours</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Actions visite --}}
        <div class="d-flex gap-2 flex-wrap">
            {{-- Badge statut --}}
            @if($valide)
                <span class="badge" style="background:var(--green-l);color:var(--green);font-size:.72rem;padding:4px 10px">
                    <i class="bi bi-patch-check me-1"></i>Validée par {{ $visite['validated_by']?->prenom }} {{ $visite['validated_by']?->nom }}
                </span>
            @elseif($sortie)
                <span class="badge" style="background:#FFF3E0;color:#D97706;font-size:.72rem;padding:4px 10px">
                    <i class="bi bi-hourglass-split me-1"></i>Terminée — en attente de validation
                </span>
            @else
                <span class="badge" style="background:var(--blue-l);color:var(--blue);font-size:.72rem;padding:4px 10px">
                    <i class="bi bi-activity me-1"></i>Visite en cours
                </span>
            @endif

            {{-- Boutons admin --}}
            @if(Auth::user()->role === 'admin')
                @if(!$valide && $sortie)
                    <form method="POST" action="{{ route('circuits.valider', $visiteId) }}">
                        @csrf
                        <button class="btn btn-sm btn-success" title="Valider cette visite">
                            <i class="bi bi-patch-check me-1"></i>Valider
                        </button>
                    </form>
                @elseif($valide)
                    <form method="POST" action="{{ route('circuits.annuler-validation', $visiteId) }}"
                          onsubmit="return confirm('Annuler la validation ?')">
                        @csrf
                        <button class="btn btn-sm btn-outline-warning" title="Annuler la validation">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </form>
                @endif
            @endif

            {{-- Toggle passages --}}
            <button class="btn btn-sm btn-outline-secondary" onclick="toggleVisit('v{{ $visiteId }}')"
                    id="btn-v{{ $visiteId }}">
                <i class="bi bi-chevron-down me-1"></i>Détails
            </button>
        </div>
    </div>

    {{-- Détails des passages (repliable) --}}
    <div id="v{{ $visiteId }}" style="display:none">
        <div class="card-body p-3">
            {{-- Ligne de temps des passages --}}
            <div class="position-relative ps-4">
                {{-- Ligne verticale --}}
                <div style="position:absolute;left:20px;top:8px;bottom:8px;width:2px;background:#EEF2F7"></div>

                @foreach($passages as $idx => $passage)
                <div class="d-flex gap-3 mb-3 position-relative">
                    {{-- Point sur la ligne --}}
                    <div style="position:absolute;left:-16px;width:14px;height:14px;border-radius:50%;
                        margin-top:3px;flex-shrink:0;
                        background:{{ $passage->type_couleur }};
                        border:2px solid #fff;
                        box-shadow:0 0 0 2px {{ $passage->type_couleur }}">
                    </div>

                    <div class="flex-grow-1 p-3 rounded" style="background:#F8FAFD;border:1px solid #EEF2F7">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <span class="fw-semibold" style="font-size:.88rem">
                                    <i class="bi {{ $passage->type_icone }} me-1" style="color:{{ $passage->type_couleur }}"></i>
                                    {{ $passage->type_label }} — {{ $passage->service->nom_service ?? '—' }}
                                </span>
                                @if($passage->type_passage === 'entree' && $passage->type_entree)
                                <span style="font-size:.75rem;color:var(--muted);margin-left:6px">
                                    ({{ $passage->type_entree_label }})
                                </span>
                                @endif
                                @if($passage->notes)
                                <div style="font-size:.8rem;color:var(--muted);margin-top:3px;font-style:italic">
                                    {{ $passage->notes }}
                                </div>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <small style="color:var(--muted);white-space:nowrap">
                                    {{ $passage->created_at->format('d/m/Y H:i') }}
                                </small>
                                @if(Auth::user()->role === 'admin')
                                <form method="POST" action="{{ route('circuits.destroy', $passage->id) }}"
                                      onsubmit="return confirm('Supprimer ce passage ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger py-0 px-1">
                                        <i class="bi bi-trash" style="font-size:.75rem"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        <div style="font-size:.76rem;color:#bbb;margin-top:4px">
                            <i class="bi bi-person me-1"></i>{{ $passage->user->prenom ?? '' }} {{ $passage->user->nom ?? '' }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Résumé durée si entrée ET sortie --}}
            @if($entree && $sortie && $visite['duree'])
            <div class="mt-2 p-2 rounded d-flex align-items-center gap-2" style="background:var(--blue-l);font-size:.82rem">
                <i class="bi bi-clock-history" style="color:var(--blue)"></i>
                Durée totale du séjour : <strong style="color:var(--blue)">{{ $visite['duree'] }}</strong>
                <span class="ms-auto" style="color:var(--muted)">
                    Du {{ $entree->created_at->format('d/m/Y H:i') }}
                    au {{ $sortie->created_at->format('d/m/Y H:i') }}
                </span>
            </div>
            @endif
        </div>
    </div>
</div>
@endforeach

@endif
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     ONGLET 2 — FACTURES (avec filtre par service)
════════════════════════════════════════════════════════════════════════ --}}
<div id="pane-factures" class="d-none">

    {{-- Filtre par service --}}
    @if($factures->count() > 0)
    <div class="card mb-3">
        <div class="card-body py-2 px-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span style="font-size:.82rem;color:var(--muted)"><i class="bi bi-funnel me-1"></i>Filtrer :</span>
                <button class="btn btn-sm btn-primary filtre-service active" data-service="tous">
                    Toutes ({{ $factures->count() }})
                </button>
                @foreach($servicesAvecFactures as $svc)
                @php $nb = $factures->where('service_id', $svc->id)->count(); @endphp
                <button class="btn btn-sm btn-outline-primary filtre-service" data-service="{{ $svc->id }}">
                    {{ $svc->nom_service }} ({{ $nb }})
                </button>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-body p-0" id="facturesContainer">
            @forelse($factures as $f)
            <div class="facture-item p-3" data-service="{{ $f->service_id }}"
                 style="border-bottom:1px solid #F0F4F8">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div class="d-flex gap-3">
                        <div style="width:38px;height:38px;border-radius:10px;flex-shrink:0;
                            background:var(--green-l);color:var(--green);
                            display:flex;align-items:center;justify-content:center;font-size:1rem">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:.9rem">
                                N° {{ $f->numero_facture }}
                                <span style="color:var(--muted);font-weight:400;font-size:.82rem;margin-left:4px">
                                    — {{ $f->service->nom_service ?? '—' }}
                                </span>
                            </div>
                            <div class="mt-1" style="font-size:.82rem">
                                {{-- Montant total --}}
                                <span style="color:var(--text)">
                                    Total : <strong>{{ number_format($f->montant,0,',',' ') }} FCFA</strong>
                                </span>
                                {{-- Prise en charge --}}
                                @if($f->has_p_e_c)
                                <span class="ms-2" style="color:var(--green)">
                                    | PEC : <strong>{{ number_format($f->pec_montant,0,',',' ') }} FCFA</strong>
                                    <span style="color:var(--muted)">({{ $f->pec_organisme }})</span>
                                </span>
                                <span class="ms-2" style="color:var(--blue)">
                                    | Solde : <strong>{{ number_format($f->montant_patient,0,',',' ') }} FCFA</strong>
                                </span>
                                @else
                                <span class="ms-2" style="color:var(--green)">
                                    | À charge : <strong>{{ number_format($f->montant,0,',',' ') }} FCFA</strong>
                                </span>
                                @endif
                            </div>
                            <div style="font-size:.76rem;color:#bbb;margin-top:3px">
                                <i class="bi bi-calendar3 me-1"></i>{{ $f->date_facture->format('d/m/Y') }}
                                &nbsp;·&nbsp;
                                <i class="bi bi-person me-1"></i>{{ $f->user->prenom ?? '' }} {{ $f->user->nom ?? '' }}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('factures.preview',$f->id) }}"
                           class="btn btn-sm btn-outline-info" title="Aperçu">
                            <i class="bi bi-eye" style="font-size:.8rem"></i>
                        </a>
                        <a href="{{ route('factures.download',$f->id) }}"
                           class="btn btn-sm btn-outline-primary" title="Télécharger PDF">
                            <i class="bi bi-download" style="font-size:.8rem"></i>
                        </a>
                        @if(Auth::user()->role === 'admin')
                        <a href="{{ route('factures.edit',$f) }}" class="btn btn-sm btn-outline-secondary" title="Modifier">
                            <i class="bi bi-pencil" style="font-size:.8rem"></i>
                        </a>
                        <form method="POST" action="{{ route('factures.destroy',$f) }}"
                              onsubmit="return confirm('Supprimer cette facture ?')" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                                <i class="bi bi-trash" style="font-size:.8rem"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5" style="color:var(--muted)">
                <i class="bi bi-receipt fs-1 d-block mb-2 opacity-25"></i>
                Aucune facture enregistrée.
                <a href="{{ route('factures.create',['patient_id'=>$patient->id]) }}" class="d-block mt-2">
                    Enregistrer une facture
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// ── Onglets ─────────────────────────────────────────────────────────────
function showTab(tab) {
    document.getElementById('pane-visites').classList.toggle('d-none', tab !== 'visites');
    document.getElementById('pane-factures').classList.toggle('d-none', tab !== 'factures');
    document.getElementById('tab-visites').classList.toggle('active', tab === 'visites');
    document.getElementById('tab-factures').classList.toggle('active', tab === 'factures');
}

// ── Toggle repliage visite ───────────────────────────────────────────────
function toggleVisit(id) {
    const el  = document.getElementById(id);
    const btn = document.getElementById('btn-'+id);
    const open = el.style.display === 'none';
    el.style.display = open ? 'block' : 'none';
    btn.innerHTML = open
        ? '<i class="bi bi-chevron-up me-1"></i>Réduire'
        : '<i class="bi bi-chevron-down me-1"></i>Détails';
}

// ── Filtre factures par service ──────────────────────────────────────────
document.querySelectorAll('.filtre-service').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filtre-service').forEach(b => {
            b.classList.remove('active','btn-primary');
            b.classList.add('btn-outline-primary');
        });
        this.classList.add('active','btn-primary');
        this.classList.remove('btn-outline-primary');

        const svc = this.dataset.service;
        document.querySelectorAll('.facture-item').forEach(item => {
            item.style.display = (svc === 'tous' || item.dataset.service === svc) ? '' : 'none';
        });
    });
});
</script>
@endsection
