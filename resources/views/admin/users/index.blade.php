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
            <div class="stat-icon" style="background:var(--red-l);color:var(--red)"><i class="bi bi-shield-fill"></i></div>
            <div><div class="stat-num" style="color:var(--red)">{{ $users->where('role','admin')->count() }}</div><div class="stat-lbl">Administrateurs</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="animation-delay:.1s">
            <div class="stat-icon" style="background:var(--green-l);color:var(--green)"><i class="bi bi-person-check-fill"></i></div>
            <div><div class="stat-num" style="color:var(--green)">{{ $users->where('role','user')->count() }}</div><div class="stat-lbl">Agents d'accueil</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="animation-delay:.15s">
            <div class="stat-icon" style="background:#F3F0FF;color:#6D28D9"><i class="bi bi-calendar-plus"></i></div>
            <div><div class="stat-num" style="color:#6D28D9">{{ $users->where('created_at','>=',now()->subDays(7))->count() }}</div><div class="stat-lbl">Cette semaine</div></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-person-gear me-2" style="color:var(--blue)"></i>Comptes utilisateurs</span>
        {{-- Barre de recherche live --}}
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
                    <th>Inscrit le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
            @foreach($users as $u)
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
                            @if($u->id===Auth::id())
                            <span style="font-size:.68rem;background:var(--blue-l);color:var(--blue);
                                border-radius:10px;padding:1px 7px;font-weight:600">Vous</span>
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
                <td style="font-size:.82rem;color:var(--muted)">{{ $u->created_at->format('d/m/Y') }}</td>
                <td class="text-end">
                    @if($u->id!==Auth::id())
                    <div class="d-flex justify-content-end gap-1 flex-wrap">
                        {{-- Changer rôle --}}
                        <form method="POST" action="{{ route('admin.users.role',$u->id) }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="role" value="{{ $u->role==='admin'?'user':'admin' }}">
                            <button type="submit"
                                    class="btn btn-sm {{ $u->role==='admin'?'btn-outline-warning':'btn-outline-success' }}"
                                    title="{{ $u->role==='admin'?'Rétrograder en agent':'Promouvoir en admin' }}"
                                    onclick="return confirm('Modifier le rôle de {{ $u->prenom }} {{ $u->nom }} ?')">
                                <i class="bi bi-arrow-left-right me-1"></i>
                                {{ $u->role==='admin'?'Rétrograder':'Promouvoir' }}
                            </button>
                        </form>
                        {{-- Supprimer --}}
                        <form method="POST" action="{{ route('admin.users.destroy',$u->id) }}" class="d-inline"
                              onsubmit="return confirm('Supprimer définitivement le compte de {{ $u->prenom }} {{ $u->nom }} ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
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
const usrSearch=document.getElementById('userSearch');
const usrRows=document.querySelectorAll('#userTableBody tr');
usrSearch.addEventListener('input',function(){
    const q=this.value.toLowerCase().trim();
    usrRows.forEach(r=>{r.style.display=q===''||r.innerText.toLowerCase().includes(q)?'':'none';});
});
</script>
@endsection
