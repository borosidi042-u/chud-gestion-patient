@extends('layouts.app')
@section('title', 'Modifier le lit')
@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil me-2"></i>Modifier le lit N°{{ $lit->numero }}
            </div>
            <div class="card-body">
                @if($lit->statut === 'occupe')
                <div class="alert alert-warning mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Attention :</strong> Ce lit est actuellement occupé par le patient
                    @if($lit->patient){{ $lit->patient->prenom }} {{ $lit->patient->nom }} (Code: {{ $lit->patient->code_unique }})@endif.
                    Le statut ne peut pas être modifié manuellement. Validez la sortie du patient d'abord.
                </div>
                @endif

                <form method="POST" action="{{ route('admin.lits.update', $lit) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Salle <span class="text-danger">*</span></label>
                        <select name="salle_id" class="form-select @error('salle_id') is-invalid @enderror" required>
                            <option value="">-- Sélectionner une salle --</option>
                            @foreach($salles as $salle)
                            <option value="{{ $salle->id }}" {{ old('salle_id', $lit->salle_id) == $salle->id ? 'selected' : '' }}>
                                {{ $salle->service->nom_service }} - {{ $salle->nom }}
                            </option>
                            @endforeach
                        </select>
                        @error('salle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Numéro du lit <span class="text-danger">*</span></label>
                        <input type="text" name="numero" class="form-control @error('numero') is-invalid @enderror"
                               value="{{ old('numero', $lit->numero) }}" required>
                        @error('numero')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Statut <span class="text-danger">*</span></label>
                        <select name="statut" class="form-select @error('statut') is-invalid @enderror"
                                {{ $lit->statut === 'occupe' ? 'disabled' : '' }} required>
                            <option value="libre" {{ old('statut', $lit->statut) == 'libre' ? 'selected' : '' }}>Libre</option>
                            <option value="maintenance" {{ old('statut', $lit->statut) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="hors_service" {{ old('statut', $lit->statut) == 'hors_service' ? 'selected' : '' }}>Hors service</option>
                        </select>
                        @if($lit->statut === 'occupe')
                        <input type="hidden" name="statut" value="{{ $lit->statut }}">
                        @endif
                        @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if($lit->statut === 'occupe')
                        <div class="form-text text-warning">Le statut ne peut pas être modifié car le lit est occupé.</div>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle me-1"></i>Enregistrer
                        </button>
                        <a href="{{ route('lits.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
