@extends('layouts.app')
@section('title', 'Gestion des salles')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-door-open me-2"></i>Gestion des salles</h4>
    <a href="{{ route('admin.salles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Ajouter une salle
    </a>
</div>


@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Service</th>
                        <th>Capacité</th>
                        <th>Nombre de lits</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salles as $salle)
                    <tr>
                        <td class="fw-semibold">{{ $salle->nom }}</td>
                        <td>{{ $salle->service->nom_service ?? '—' }}</td>
                        <td>{{ $salle->capacite }}</td>
                        <td>
                            <span class="badge bg-info">{{ $salle->lits_count }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.salles.edit', $salle) }}" class="btn btn-sm btn-outline-secondary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.salles.destroy', $salle) }}" class="d-inline"
                                  onsubmit="return confirm('Supprimer cette salle ? Les lits associés seront également supprimés.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" {{ $salle->lits_count > 0 ? 'disabled title="Supprimer d\'abord les lits"' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                            Aucune salle enregistrée.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
