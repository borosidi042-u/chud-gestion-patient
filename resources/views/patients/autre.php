@extends('layouts.app')
@section('title', 'Liste des patients')
@section('content')

{{-- Statistique pour la période --}}
<div class="row g-3 mb-4">
    <div class="col-md-4 mx-auto">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--blue-l);color:var(--blue)"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="stat-num" style="color:var(--blue)">{{ $totalPatients ?? $patients->total() }}</div>
                <div class="stat-lbl">Total patients</div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <span style="color:var(--muted);font-size:.88rem">{{ $patients->total() }} patient(s)</span>
    <a href="{{ route('patients.create') }}" class="btn btn-success"><i class="bi bi-person-plus me-1"></i>Nouveau patient</a>
</div>

{{-- Filtres et recherche --}}
<div class="card mb-3">
    <div class="card-body py-3 px-4">
        <form method="GET" id="filterForm" novalidate>
            <div class="row g-3 align-items-end">
                {{-- Barre de recherche --}}
                <div class="col-md-4">
                    <label class="form-label" style="font-size:.8rem">Recherche</label>
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute" style="left:11px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.85rem"></i>
                        <input type="text" name="search" id="searchInput" class="form-control"
                               style="padding-left:32px" value="{{ request('search') }}"
                               placeholder="Nom, prénom, code, NPI, téléphone...">
                    </div>
                </div>

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

{{-- Liste des patients --}}
<div class="card">
    <div class="card-body p-0">
        @if($patients->isEmpty())
        <div class="text-center py-5" style="color:var(--muted)">
            <i class="bi bi-person-x fs-1 d-block mb-2 opacity-25"></i>
            Aucun patient trouvé.
            @if(request('search') || request('date_start') || request('date_end'))
                <a href="{{ route('patients.index') }}" class="d-block mt-2">Voir tous les patients</a>
            @endif
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Nom complet</th>
                        <th>Téléphone</th>
                        <th>NPI</th>
                        <th>Enregistré le</th>
                        <th>Par</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $p)
                    <tr>
                        <td><span class="code-badge">{{ $p->code_unique }}</span></td>
                        <td>
                            <a href="{{ route('patients.show', $p) }}" class="fw-semibold text-decoration-none" style="color:var(--text)">
                                {{ $p->prenom }} {{ $p->nom }}
                            </a>
                        </td>
                        <td>{{ $p->telephone ?? '—' }}</td>
                        <td>{{ $p->npi ?? '—' }}</td>
                        <td style="font-size:.84rem">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                        <td style="font-size:.78rem;color:var(--muted)">{{ $p->user->prenom ?? '' }} {{ $p->user->nom ?? '' }}</td>
                        <td class="text-end">
                            <a href="{{ route('patients.show', $p) }}" class="btn btn-sm btn-outline-primary me-1" title="Voir">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('patients.edit', $p) }}" class="btn btn-sm btn-outline-secondary me-1" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('patients.destroy', $p) }}" class="d-inline"
                                  onsubmit="return confirm('Supprimer définitivement ce patient ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @if($patients->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-center">{{ $patients->links() }}</div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
// Validation des dates
const dateStart = document.getElementById('dateStart');
const dateEnd = document.getElementById('dateEnd');

if (dateStart && dateEnd) {
    dateStart.addEventListener('change', function() {
        if (dateEnd.value && this.value > dateEnd.value) {
            alert('La date de début ne peut pas être postérieure à la date de fin.');
            this.value = '';
        }
    });

    dateEnd.addEventListener('change', function() {
        if (dateStart.value && this.value < dateStart.value) {
            alert('La date de fin ne peut pas être antérieure à la date de début.');
            this.value = '';
        }
    });
}

// Recherche automatique après saisie
let timeout = null;
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500);
    });
}
</script>
@endsection
