@extends('layouts.app')
@section('title', 'Ajouter un lit')
@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card animate__animated animate__fadeIn">
            <div class="card-header">
                <i class="bi bi-plus-circle me-2"></i>Ajouter un lit
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.lits.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Salle <span class="text-danger">*</span></label>
                        <select name="salle_id" class="form-select @error('salle_id') is-invalid @enderror" required>
                            <option value="">-- Sélectionner une salle --</option>
                            @foreach($salles as $salle)
                            <option value="{{ $salle->id }}" {{ old('salle_id') == $salle->id ? 'selected' : '' }}
                                    data-capacite="{{ $salle->capacite }}"
                                    data-lits="{{ $salle->lits->count() }}">
                                {{ $salle->service->nom_service }} - {{ $salle->nom }}
                                ({{ $salle->lits->count() }}/{{ $salle->capacite }} lits)
                            </option>
                            @endforeach
                        </select>
                        @error('salle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text" id="capaciteInfo"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Numéro du lit <span class="text-danger">*</span></label>
                        <input type="text" name="numero" class="form-control @error('numero') is-invalid @enderror"
                               value="{{ old('numero') }}" placeholder="Ex: 101, A12, 05..." required>
                        @error('numero')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Numéro unique dans la salle choisie.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Statut initial <span class="text-danger">*</span></label>
                        <select name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                            <option value="libre" {{ old('statut') == 'libre' ? 'selected' : '' }}>Libre</option>
                            <option value="maintenance" {{ old('statut') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="hors_service" {{ old('statut') == 'hors_service' ? 'selected' : '' }}>Hors service</option>
                        </select>
                        @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Le statut "Occupé" est géré automatiquement par le système.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="bi bi-check-circle me-1"></i>Ajouter
                        </button>
                        <a href="{{ route('lits.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelector('select[name="salle_id"]').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const capacite = option.dataset.capacite;
    const litsActuels = option.dataset.lits;
    const infoDiv = document.getElementById('capaciteInfo');

    if (capacite && litsActuels) {
        if (parseInt(litsActuels) >= parseInt(capacite)) {
            infoDiv.innerHTML = '<span class="text-danger">⚠️ Cette salle a atteint sa capacité maximale (' + capacite + ' lits). Impossible d\'ajouter un nouveau lit.</span>';
            document.getElementById('btnSubmit').disabled = true;
        } else {
            infoDiv.innerHTML = '<span class="text-success">✓ Cette salle peut encore accueillir ' + (capacite - litsActuels) + ' lit(s).</span>';
            document.getElementById('btnSubmit').disabled = false;
        }
    }
});

// Déclencher le changement initial
document.querySelector('select[name="salle_id"]').dispatchEvent(new Event('change'));
</script>

@endsection
