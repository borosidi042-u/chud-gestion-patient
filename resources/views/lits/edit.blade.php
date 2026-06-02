@extends('layouts.app')
@section('title', 'Modifier le lit')
@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card animate__animated animate__fadeIn">
            <div class="card-header">
                <i class="bi bi-pencil me-2"></i>Modifier le lit N°{{ $lit->numero }}
            </div>
            <div class="card-body">
                @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                @if($lit->statut === 'occupe')
                <div class="alert alert-warning mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Attention :</strong> Ce lit est actuellement occupé par le patient
                    @if($lit->patient){{ $lit->patient->prenom }} {{ $lit->patient->nom }} (Code: {{ $lit->patient->code_unique }})@endif.
                    Le statut ne peut pas être modifié manuellement. Validez la sortie du patient d'abord.
                </div>
                @endif

                <form method="POST" action="{{ route('admin.lits.update', $lit) }}" id="formEditLit">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Salle <span class="text-danger">*</span></label>
                        <select name="salle_id" id="salle_id" class="form-select @error('salle_id') is-invalid @enderror" required>
                            <option value="">-- Sélectionner une salle --</option>
                            @foreach($salles as $salle)
                            <option value="{{ $salle->id }}" {{ old('salle_id', $lit->salle_id) == $salle->id ? 'selected' : '' }}
                                    data-capacite="{{ $salle->capacite }}"
                                    data-lits="{{ $salle->lits->count() }}">
                                {{ $salle->service->nom_service }} - {{ $salle->nom }}
                                ({{ $salle->lits->count() }}/{{ $salle->capacite }} lits)
                            </option>
                            @endforeach
                        </select>
                        @error('salle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div id="capaciteInfo" class="form-text"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Numéro du lit <span class="text-danger">*</span></label>
                        <input type="text" name="numero" id="numero" class="form-control @error('numero') is-invalid @enderror"
                               value="{{ old('numero', $lit->numero) }}" required>
                        @error('numero')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Numéro unique dans la salle choisie.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Statut <span class="text-danger">*</span></label>
                        <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror"
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

                    <div class="alert alert-info small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Le statut "Occupé" est géré automatiquement par le système lors des admissions et sorties.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning" id="btnSubmit">
                            <i class="bi bi-check-circle me-1"></i>Enregistrer
                        </button>
                        <a href="{{ route('lits.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Vérification de la capacité lors du changement de salle
const salleSelect = document.getElementById('salle_id');
const capaciteInfo = document.getElementById('capaciteInfo');
const btnSubmit = document.getElementById('btnSubmit');

function checkCapacity() {
    if (salleSelect && capaciteInfo) {
        const selectedOption = salleSelect.options[salleSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const capacite = parseInt(selectedOption.dataset.capacite);
            const litsActuels = parseInt(selectedOption.dataset.lits);
            const litActuel = {{ $lit->id }};

            if (litsActuels >= capacite) {
                capaciteInfo.innerHTML = '<span class="text-danger">⚠️ Cette salle a atteint sa capacité maximale (' + capacite + ' lits). Impossible de modifier.</span>';
                if (btnSubmit) btnSubmit.disabled = true;
            } else {
                const placesLibres = capacite - litsActuels;
                capaciteInfo.innerHTML = '<span class="text-success">✓ Cette salle peut encore accueillir ' + placesLibres + ' lit(s).</span>';
                if (btnSubmit) btnSubmit.disabled = false;
            }
        } else {
            capaciteInfo.innerHTML = '';
            if (btnSubmit) btnSubmit.disabled = true;
        }
    }
}

if (salleSelect) {
    salleSelect.addEventListener('change', checkCapacity);
    // Vérification initiale
    checkCapacity();
}
</script>

@endsection
