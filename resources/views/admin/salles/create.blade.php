@extends('layouts.app')
@section('title', 'Ajouter une salle')
@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card animate__animated animate__fadeIn">
            <div class="card-header">
                <i class="bi bi-plus-circle me-2"></i>Ajouter une salle
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.salles.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Service <span class="text-danger">*</span></label>
                        <select name="service_id" class="form-select @error('service_id') is-invalid @enderror" required>
                            <option value="">-- Sélectionner un service --</option>
                            @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->nom_service }}
                            </option>
                            @endforeach
                        </select>
                        @error('service_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nom de la salle <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                               value="{{ old('nom') }}" placeholder="Ex: Salle A, Salle d'attente, Réanimation..." required>
                        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description (optionnelle)</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Informations supplémentaires...">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Capacité maximale (nombre de lits) <span class="text-danger">*</span></label>
                        <input type="number" name="capacite" class="form-control @error('capacite') is-invalid @enderror"
                               value="{{ old('capacite', 1) }}" min="1" max="200" required>
                        @error('capacite')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Nombre maximal de lits que cette salle peut contenir. Vous ne pourrez pas ajouter plus de lits que cette capacité.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Ajouter
                        </button>
                        <a href="{{ route('admin.salles.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
