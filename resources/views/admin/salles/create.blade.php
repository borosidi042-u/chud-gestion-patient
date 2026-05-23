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
                @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <form method="POST" action="{{ route('admin.salles.store') }}" id="formSalle" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Service <span class="text-danger">*</span></label>
                        <select name="service_id" id="service_id" class="form-select @error('service_id') is-invalid @enderror">
                            <option value="">-- Sélectionner un service --</option>
                            @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->nom_service }}
                            </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="serviceErr">Veuillez sélectionner un service.</div>
                        @error('service_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nom de la salle <span class="text-danger">*</span></label>
                        <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror"
                               value="{{ old('nom') }}" placeholder="Ex: Salle A, Salle d'attente, Réanimation...">
                        <div class="invalid-feedback" id="nomErr">Le nom de la salle est requis.</div>
                        @error('nom')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description (optionnelle)</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Informations supplémentaires...">{{ old('description') }}</textarea>
                        @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Capacité maximale (nombre de lits) <span class="text-danger">*</span></label>
                        <input type="number" name="capacite" id="capacite" class="form-control @error('capacite') is-invalid @enderror"
                               value="{{ old('capacite', 1) }}" min="1" max="200">
                        <div class="invalid-feedback" id="capaciteErr">La capacité doit être au minimum 1.</div>
                        @error('capacite')<div class="text-danger small">{{ $message }}</div>@enderror
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
const serviceSelect = document.getElementById('service_id');
if(serviceSelect) {
    serviceSelect.addEventListener('change', function() {
        resetFieldError('service_id', 'serviceErr');
    });
    serviceSelect.addEventListener('focus', function() {
        resetFieldError('service_id', 'serviceErr');
    });
}

const nomField = document.getElementById('nom');
if(nomField) {
    nomField.addEventListener('input', function() {
        resetFieldError('nom', 'nomErr');
    });
    nomField.addEventListener('focus', function() {
        resetFieldError('nom', 'nomErr');
    });
}

const capaciteField = document.getElementById('capacite');
if(capaciteField) {
    capaciteField.addEventListener('input', function() {
        resetFieldError('capacite', 'capaciteErr');
    });
    capaciteField.addEventListener('focus', function() {
        resetFieldError('capacite', 'capaciteErr');
    });
}

// Validation avant soumission
document.getElementById('formSalle').addEventListener('submit', function(e) {
    let isValid = true;

    const serviceId = document.getElementById('service_id').value;
    const nom = document.getElementById('nom').value.trim();
    const capacite = document.getElementById('capacite').value;

    // Réinitialiser toutes les erreurs
    resetFieldError('service_id', 'serviceErr');
    resetFieldError('nom', 'nomErr');
    resetFieldError('capacite', 'capaciteErr');

    // Validation du service
    if(!serviceId) {
        showFieldError('service_id', 'serviceErr');
        isValid = false;
    }

    // Validation du nom
    if(!nom) {
        showFieldError('nom', 'nomErr');
        isValid = false;
    }

    // Validation de la capacité
    if(!capacite || parseInt(capacite) < 1) {
        showFieldError('capacite', 'capaciteErr');
        isValid = false;
    }

    if(!isValid) {
        e.preventDefault();
    }
});
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
