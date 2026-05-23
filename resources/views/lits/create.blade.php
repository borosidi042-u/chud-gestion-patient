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
                @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <form method="POST" action="{{ route('admin.lits.store') }}" id="formLit" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Salle <span class="text-danger">*</span></label>
                        <select name="salle_id" id="salle_id" class="form-select @error('salle_id') is-invalid @enderror">
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
                        <div class="invalid-feedback" id="salleErr">Veuillez sélectionner une salle.</div>
                        @error('salle_id')<div class="text-danger small">{{ $message }}</div>@enderror
                        <div class="form-text" id="capaciteInfo"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Numéro du lit <span class="text-danger">*</span></label>
                        <input type="text" name="numero" id="numero" class="form-control @error('numero') is-invalid @enderror"
                               value="{{ old('numero') }}" placeholder="Ex: 101, A12, 05...">
                        <div class="invalid-feedback" id="numeroErr">Le numéro du lit est requis.</div>
                        @error('numero')<div class="text-danger small">{{ $message }}</div>@enderror
                        <div class="form-text">Numéro unique dans la salle choisie.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Statut initial <span class="text-danger">*</span></label>
                        <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror">
                            <option value="libre" {{ old('statut') == 'libre' ? 'selected' : '' }}>Libre</option>
                            <option value="maintenance" {{ old('statut') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="hors_service" {{ old('statut') == 'hors_service' ? 'selected' : '' }}>Hors service</option>
                        </select>
                        <div class="invalid-feedback" id="statutErr">Veuillez sélectionner un statut.</div>
                        @error('statut')<div class="text-danger small">{{ $message }}</div>@enderror
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
// Fonction pour réinitialiser l'erreur d'un champ
function resetFieldError(fieldId, errId) {
    const field = document.getElementById(fieldId);
    const errDiv = document.getElementById(errId);
    if(field) {
        field.classList.remove('is-invalid');
    }
    if(errDiv) {
        errDiv.style.display = 'none';
    }
}

// Fonction pour afficher l'erreur d'un champ
function showFieldError(fieldId, errId) {
    const field = document.getElementById(fieldId);
    const errDiv = document.getElementById(errId);
    if(field) {
        field.classList.add('is-invalid');
    }
    if(errDiv) {
        errDiv.style.display = 'block';
    }
}

// Réinitialiser les erreurs quand l'utilisateur tape ou change la valeur
const numeroField = document.getElementById('numero');
if(numeroField) {
    numeroField.addEventListener('input', function() {
        resetFieldError('numero', 'numeroErr');
    });
    numeroField.addEventListener('focus', function() {
        resetFieldError('numero', 'numeroErr');
    });
}

const salleSelect = document.getElementById('salle_id');
if(salleSelect) {
    salleSelect.addEventListener('change', function() {
        resetFieldError('salle_id', 'salleErr');

        // Vérification de la capacité
        const option = this.options[this.selectedIndex];
        const capacite = option.dataset.capacite;
        const litsActuels = option.dataset.lits;
        const infoDiv = document.getElementById('capaciteInfo');
        const btnSubmit = document.getElementById('btnSubmit');

        if (capacite && litsActuels && this.value) {
            if (parseInt(litsActuels) >= parseInt(capacite)) {
                infoDiv.innerHTML = '<span class="text-danger">⚠️ Cette salle a atteint sa capacité maximale (' + capacite + ' lits). Impossible d\'ajouter un nouveau lit.</span>';
                if(btnSubmit) btnSubmit.disabled = true;
            } else {
                infoDiv.innerHTML = '<span class="text-success">✓ Cette salle peut encore accueillir ' + (capacite - litsActuels) + ' lit(s).</span>';
                if(btnSubmit) btnSubmit.disabled = false;
            }
        } else if(this.value) {
            infoDiv.innerHTML = '';
            if(btnSubmit) btnSubmit.disabled = false;
        } else {
            infoDiv.innerHTML = '';
            if(btnSubmit) btnSubmit.disabled = true;
        }
    });
    salleSelect.addEventListener('focus', function() {
        resetFieldError('salle_id', 'salleErr');
    });
}

const statutSelect = document.getElementById('statut');
if(statutSelect) {
    statutSelect.addEventListener('change', function() {
        resetFieldError('statut', 'statutErr');
    });
    statutSelect.addEventListener('focus', function() {
        resetFieldError('statut', 'statutErr');
    });
}

// Validation avant soumission
document.getElementById('formLit').addEventListener('submit', function(e) {
    let isValid = true;

    const salleId = document.getElementById('salle_id').value;
    const numero = document.getElementById('numero').value.trim();
    const statut = document.getElementById('statut').value;

    // Réinitialiser toutes les erreurs
    resetFieldError('salle_id', 'salleErr');
    resetFieldError('numero', 'numeroErr');
    resetFieldError('statut', 'statutErr');

    // Validation de la salle
    if(!salleId) {
        showFieldError('salle_id', 'salleErr');
        isValid = false;
    }

    // Validation du numéro
    if(!numero) {
        showFieldError('numero', 'numeroErr');
        isValid = false;
    }

    // Validation du statut
    if(!statut) {
        showFieldError('statut', 'statutErr');
        isValid = false;
    }

    // Vérification que la salle n'est pas pleine
    const salleSelect = document.getElementById('salle_id');
    const selectedOption = salleSelect.options[salleSelect.selectedIndex];
    if(salleId && selectedOption && selectedOption.dataset.capacite && selectedOption.dataset.lits) {
        const litsActuels = parseInt(selectedOption.dataset.lits);
        const capacite = parseInt(selectedOption.dataset.capacite);
        if(litsActuels >= capacite) {
            const infoDiv = document.getElementById('capaciteInfo');
            infoDiv.innerHTML = '<span class="text-danger">⚠️ Cette salle est pleine. Impossible d\'ajouter un lit.</span>';
            showFieldError('salle_id', 'salleErr');
            isValid = false;
        }
    }

    if(!isValid) {
        e.preventDefault();
    }
});

// Déclencher le changement initial
if(salleSelect) {
    salleSelect.dispatchEvent(new Event('change'));
}
</script>

<style>
.invalid-feedback {
    display: none;
}
.is-invalid ~ .invalid-feedback,
.invalid-feedback.show {
    display: block;
}
</style>

@endsection
