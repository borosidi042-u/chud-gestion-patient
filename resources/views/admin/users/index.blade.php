@extends('layouts.app')
@section('title','Gestion des utilisateurs')
@section('content')

{{-- Statistiques rapides --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--blue-l);color:var(--blue)"><i class="bi bi-people-fill"></i></div>
            <div><div class="stat-num" style="color:var(--blue)">{{ $users->count() }}</div><div class="stat-lbl">Total comptes</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="animation-delay:.05s">
            <div class="stat-icon" style="background:var(--amber-l,#FFF3E0);color:var(--amber,#D97706)"><i class="bi bi-clock-history"></i></div>
            <div><div class="stat-num" style="color:var(--amber,#D97706)">{{ $pendingUsers->count() }}</div><div class="stat-lbl">En attente</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="animation-delay:.1s">
            <div class="stat-icon" style="background:var(--red-l);color:var(--red)"><i class="bi bi-shield-fill"></i></div>
            <div><div class="stat-num" style="color:var(--red)">{{ $users->where('role','admin')->count() }}</div><div class="stat-lbl">Administrateurs</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="animation-delay:.15s">
            <div class="stat-icon" style="background:var(--green-l);color:var(--green)"><i class="bi bi-person-check-fill"></i></div>
            <div><div class="stat-num" style="color:var(--green)">{{ $users->where('role','user')->count() }}</div><div class="stat-lbl">Agents d'accueil</div></div>
        </div>
    </div>
</div>

{{-- Section des comptes en attente de validation --}}
@if($pendingUsers->count() > 0)
<div class="card mb-4 border-warning">
    <div class="card-header bg-warning bg-opacity-10 d-flex align-items-center gap-2">
        <i class="bi bi-clock-history" style="color:var(--amber,#D97706)"></i>
        <span class="fw-semibold">Comptes en attente de validation</span>
        <span class="badge bg-warning ms-2">{{ $pendingUsers->count() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Inscrit le</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingUsers as $u)
                    <tr style="background:#FFFDF5">
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;border-radius:50%;background:var(--amber-l,#FFF3E0);color:var(--amber,#D97706);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.82rem">
                                    {{ strtoupper(substr($u->prenom,0,1).substr($u->nom,0,1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold" style="font-size:.88rem">{{ $u->prenom }} {{ $u->nom }}</div>
                                    <span style="font-size:.68rem;background:var(--amber-l);color:var(--amber);border-radius:10px;padding:1px 7px;">En attente</span>
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--muted);font-size:.85rem">{{ $u->email }}</td>
                        <td style="font-size:.82rem;color:var(--muted)">{{ $u->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('admin.users.approve', $u->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success"
                                        onclick="return confirm('Valider l\'inscription de {{ $u->prenom }} {{ $u->nom }} ?')">
                                    <i class="bi bi-check-circle me-1"></i>Valider
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" class="d-inline"
                                  onsubmit="return confirm('Refuser et supprimer ce compte ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-x-circle me-1"></i>Refuser
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Section des comptes validés --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-person-gear me-2" style="color:var(--blue)"></i>Comptes validés</span>
        <div class="position-relative" style="width:260px">
            <i class="bi bi-search position-absolute" style="left:11px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.85rem"></i>
            <input type="text" id="userSearch" class="form-control form-control-sm"
                   style="padding-left:32px;border-radius:8px" placeholder="Rechercher un utilisateur…">
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Inscrit le</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    @foreach($approvedUsers as $u)
                    @php
                        // Compter les données liées à cet utilisateur
                        $userFacturesCount = $u->factures()->count();
                        $userCircuitsCount = $u->circuits()->count();
                        $hasData = ($userFacturesCount > 0 || $userCircuitsCount > 0);
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;border-radius:50%;flex-shrink:0;
                                    background:{{ $u->role==='admin' ? 'var(--red-l)' : 'var(--blue-l)' }};
                                    color:{{ $u->role==='admin' ? 'var(--red)' : 'var(--blue)' }};
                                    display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.82rem">
                                    {{ strtoupper(substr($u->prenom,0,1).substr($u->nom,0,1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold" style="font-size:.88rem">{{ $u->prenom }} {{ $u->nom }}</div>
                                    @if($u->id === Auth::id())
                                    <span style="font-size:.68rem;background:var(--blue-l);color:var(--blue);border-radius:10px;padding:1px 7px;font-weight:600">Vous</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--muted);font-size:.85rem">{{ $u->email }}</td>
                        <td>
                            <span style="border-radius:20px;padding:4px 12px;font-size:.72rem;font-weight:600;
                                background:{{ $u->role==='admin' ? 'var(--red-l)' : 'var(--blue-l)' }};
                                color:{{ $u->role==='admin' ? 'var(--red)' : 'var(--blue)' }}">
                                <i class="bi {{ $u->role==='admin' ? 'bi-shield-fill' : 'bi-person-fill' }} me-1"></i>
                                {{ $u->role==='admin' ? 'Administrateur' : "Agent d'accueil" }}
                            </span>
                        </td>
                        <td>
                            @if($u->approved)
                            <span style="font-size:.72rem;color:var(--green)"><i class="bi bi-check-circle-fill me-1"></i>Validé</span>
                            @else
                            <span style="font-size:.72rem;color:var(--amber)"><i class="bi bi-clock-fill me-1"></i>En attente</span>
                            @endif
                        </td>
                        <td style="font-size:.82rem;color:var(--muted)">{{ $u->created_at->format('d/m/Y') }}</td>
                        <td class="text-end">
                            @if($u->id !== Auth::id())
                            <div class="d-flex justify-content-end gap-1 flex-wrap">
                                {{-- Désactiver/Activer --}}
                                @if($u->approved)
                                <form method="POST" action="{{ route('admin.users.disapprove', $u->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning"
                                            onclick="return confirm('Désactiver le compte de {{ $u->prenom }} {{ $u->nom }} ?')"
                                            title="Désactiver le compte">
                                        <i class="bi bi-ban me-1"></i>Désactiver
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('admin.users.approve', $u->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success"
                                            onclick="return confirm('Activer le compte de {{ $u->prenom }} {{ $u->nom }} ?')"
                                            title="Activer le compte">
                                        <i class="bi bi-check-circle me-1"></i>Activer
                                    </button>
                                </form>
                                @endif

                                {{-- Changer rôle --}}
                                <form method="POST" action="{{ route('admin.users.role', $u->id) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="role" value="{{ $u->role==='admin'?'user':'admin' }}">
                                    <button type="submit"
                                            class="btn btn-sm {{ $u->role==='admin'?'btn-outline-info':'btn-outline-primary' }}"
                                            title="{{ $u->role==='admin'?'Rétrograder en agent':'Promouvoir en admin' }}"
                                            onclick="return confirm('Modifier le rôle de {{ $u->prenom }} {{ $u->nom }} ?')">
                                        <i class="bi bi-arrow-left-right me-1"></i>
                                        {{ $u->role==='admin'?'Rétrograder':'Promouvoir' }}
                                    </button>
                                </form>

                                {{-- Supprimer ou Transférer --}}
                                @if($hasData)
                                    <a href="{{ route('admin.users.transfer.form', $u->id) }}"
                                       class="btn btn-sm btn-outline-warning"
                                       title="Ce compte a {{ $userFacturesCount }} facture(s) et {{ $userCircuitsCount }} passage(s). Transférer avant suppression">
                                        <i class="bi bi-shuffle me-1"></i>Transférer
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" class="d-inline"
                                          onsubmit="return confirm('Supprimer définitivement le compte de {{ $u->prenom }} {{ $u->nom }} ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                            @else
                            <span style="font-size:.78rem;color:var(--muted)">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Recherche live dans le tableau utilisateurs
const usrSearch = document.getElementById('userSearch');
const usrRows = document.querySelectorAll('#userTableBody tr');
if (usrSearch) {
    usrSearch.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        usrRows.forEach(r => {
            r.style.display = (q === '' || r.innerText.toLowerCase().includes(q)) ? '' : 'none';
        });
    });
}
</script>
@endsection
