@extends('layouts.app')
@section('title', 'Services hospitaliers')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <span style="color:var(--muted);font-size:.88rem">{{ $services->count() }} service(s)</span>
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary animate__animated animate__pulse">
        <i class="bi bi-plus-circle me-1"></i>Nouveau service
    </a>
</div>

<div class="row">
    @forelse($services as $service)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card service-card h-100 animate__animated animate__fadeInUp" style="animation-delay: {{ $loop->index * 0.05 }}s;">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-building me-2" style="color:var(--blue)"></i>
                    {{ $service->nom_service }}
                </h5>
            </div>
            <div class="card-body">
                @if($service->description)
                <p class="text-muted small mb-3">{{ $service->description }}</p>
                @endif

                <div class="service-stats">
                    <div class="stat-item">
                        <i class="bi bi-door-open"></i>
                        <span>{{ $service->salles_count }} salle(s)</span>
                    </div>
                    <div class="stat-item">
                        <i class="bi bi-hospital"></i>
                        <span>{{ $service->lits_count }} lit(s)</span>
                    </div>
                    <div class="stat-item">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>{{ $service->lits_libres }} libre(s)</span>
                    </div>
                    <div class="stat-item">
                        <i class="bi bi-person-fill text-danger"></i>
                        <span>{{ $service->lits_occupes }} occupé(s)</span>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white text-end">
                <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-outline-secondary me-1">
                    <i class="bi bi-pencil"></i> Modifier
                </a>
                <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="d-inline"
                      onsubmit="return confirm('Supprimer le service {{ $service->nom_service }} ? Toutes les salles et lits associés seront également supprimés.')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" {{ ($service->salles_count > 0) ? 'disabled' : '' }}>
                        <i class="bi bi-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5" style="color:var(--muted)">
            <i class="bi bi-building fs-1 d-block mb-2 opacity-25"></i>
            Aucun service. <a href="{{ route('admin.services.create') }}">Ajouter un service</a>
        </div>
    </div>
    @endforelse
</div>

@endsection

@section('scripts')
<style>
.service-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.service-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 10px;
}

.stat-item {
    background: #F8FAFD;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 6px;
}
</style>
@endsection
