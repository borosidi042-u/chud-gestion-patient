@extends('layouts.app')
@section('title', 'Liste des patients')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <span style="color:var(--muted);font-size:.88rem">
        <i class="bi bi-people me-1"></i><strong>{{ $patients->total() }}</strong> patient(s) enregistré(s)
    </span>
    <a href="{{ route('patients.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Nouveau patient
    </a>
</div>

<div class="card mb-3">
    <div class="card-body py-3 px-4">
        <div class="d-flex gap-2 flex-wrap align-items-center">

            <div class="position-relative flex-grow-1" style="max-width:420px">

                <!-- Icône -->
                <i class="bi bi-search position-absolute"
                   style="left:12px; top:50%; transform:translateY(-50%);
                          font-size:16px; color:var(--muted); pointer-events:none;">
                </i>

                <!-- Input -->
                <input type="text"
                       id="searchInput"
                       name="search"
                       value="{{ request('search') }}"
                       class="form-control"
                       style="padding-left:38px;"
                       placeholder="Nom, prénom, code, téléphone ou NPI…"
                       autocomplete="off">
            </div>

            <form method="GET" id="searchForm">
                <input type="hidden" name="search" id="searchHidden" value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>Rechercher
                </button>
            </form>

            @if(request('search'))
            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x me-1"></i>Effacer
            </a>
            @endif

        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-3 px-4">
        <form method="GET" id="filterForm" novalidate>
            <div class="row g-3 align-items-end">
                {{-- Date début --}}
                <div class="col-md-3">
                    <label class="form-label" style="font-size:.8rem">Du (date d'enregistrement)</label>
                    <input type="date" name="date_start" id="dateStart" class="form-control"
                           value="{{ request('date_start') }}" max="{{ date('Y-m-d') }}">
                </div>

                {{-- Date fin --}}
                <div class="col-md-3">
                    <label class="form-label" style="font-size:.8rem">Au (date d'enregistrement)</label>
                    <input type="date" name="date_end" id="dateEnd" class="form-control"
                           value="{{ request('date_end') }}" max="{{ date('Y-m-d') }}">
                </div>

                {{-- Boutons --}}
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-1"></i>Filtrer
                        </button>
                        @if(request('search') || request('date_start') || request('date_end'))
                        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary" title="Effacer les filtres">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        {{-- Affichage des filtres actifs --}}
        @if(request('date_start') || request('date_end'))
        <div class="mt-2 pt-2" style="border-top:1px solid #e5e7eb">
            <span style="font-size:.75rem;color:var(--muted)">
                <i class="bi bi-info-circle me-1"></i>
                @if(request('date_start') && request('date_end'))
                    Période d'enregistrement : du {{ \Carbon\Carbon::parse(request('date_start'))->format('d/m/Y') }} au {{ \Carbon\Carbon::parse(request('date_end'))->format('d/m/Y') }}
                @elseif(request('date_start'))
                    À partir du {{ \Carbon\Carbon::parse(request('date_start'))->format('d/m/Y') }}
                @elseif(request('date_end'))
                    Jusqu'au {{ \Carbon\Carbon::parse(request('date_end'))->format('d/m/Y') }}
                @endif
            </span>
        </div>
        @endif
    </div>
</div>
<div class="card">
    <div class="card-body p-0">
        @if($patients->isEmpty())
        <div class="text-center py-5" style="color:var(--muted)">
            <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
            @if(request('search'))
                Aucun résultat pour « <strong>{{ request('search') }}</strong> »
            @else
                Aucun patient. <a href="{{ route('patients.create') }}">Enregistrer le premier</a>
            @endif
        </div>
        @else
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Code unique</th>
                    <th>Nom complet</th>
                    <th>Téléphone</th>
                    <th>NPI</th>
                    <th>Date naissance</th>
                    <th>Enregistré par</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="patientTableBody">
            @foreach($patients as $patient)
            <tr>
                <td><span class="code-badge">{{ $patient->code_unique }}</span></td>
                <td>
                    <a href="{{ route('patients.show',$patient) }}" class="fw-semibold text-decoration-none" style="color:var(--text)">
                       {{ $patient->nom }} {{ $patient->prenom }}
                    </a>
                </td>
                <td>{{ $patient->telephone ?? '—' }}</td>
                <td>{{ $patient->npi ?? '—' }}</td>
                <td>{{ $patient->date_naissance ? $patient->date_naissance->format('d/m/Y') : '—' }}</td>
                <td style="font-size:.8rem;color:var(--muted)">{{ $patient->user->prenom ?? '' }} {{ $patient->user->nom ?? '' }}</td>
                <td class="text-end">
                    <div class="d-flex justify-content-end flex-wrap gap-1">

                        <a href="{{ route('patients.show',$patient) }}"
                        class="btn btn-sm btn-outline-primary"
                        title="Voir">
                            <i class="bi bi-eye"></i>
                        </a>

                        <a href="{{ route('patients.edit',$patient) }}"
                        class="btn btn-sm btn-outline-secondary"
                        title="Modifier">
                            <i class="bi bi-pencil"></i>
                        </a>

                        <form method="POST"
                            action="{{ route('patients.destroy',$patient) }}"
                            onsubmit="return confirm('Supprimer {{ $patient->prenom }} {{ $patient->nom }} ? Cette action est irréversible.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>

                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>
    @if($patients->hasPages())
    <div class="card-footer"><div class="d-flex justify-content-center">{{ $patients->links() }}</div></div>
    @endif
</div>
@endsection

@section('scripts')
<script>
// Recherche en temps réel (filtre instantané côté client)
const input = document.getElementById('searchInput');
const hidden = document.getElementById('searchHidden');
const form   = document.getElementById('searchForm');
const rows   = document.querySelectorAll('#patientTableBody tr');

input.addEventListener('input', function() {
    const val = this.value.toLowerCase().trim();
    hidden.value = this.value;
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = val === '' || text.includes(val) ? '' : 'none';
    });
});

// Sync input → form hidden avant submit
form.addEventListener('submit', () => { hidden.value = input.value; });
</script>
@endsection
