@extends('layouts.app')
@section('title','Modifier le service')
@section('content')
<div class="row justify-content-center"><div class="col-lg-5">
<div class="card">
    <div class="card-header"><i class="bi bi-pencil me-2" style="color:var(--amber)"></i>Modifier le service</div>
    <div class="card-body p-4">
        @if($errors->any())
        <div class="alert alert-danger mb-3"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ route('admin.services.update',$service) }}" id="formSvcE" novalidate>
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nom du service <span class="text-danger">*</span></label>
                <input type="text" name="nom_service" id="nomSvcE" value="{{ old('nom_service',$service->nom_service) }}"
                       class="form-control @error('nom_service') is-invalid @enderror">
                <div class="invalid-feedback" id="nomSvcE-err"></div>
                <div class="field-hint">Lettres, chiffres, espaces et tirets. Ne peut pas être uniquement des chiffres.</div>
            </div>
            <div class="mb-4">
                <label class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description',$service->description) }}</textarea>
                <div class="field-hint" id="descHint">Maximum 500 caractères.</div>
                <div class="invalid-feedback" id="desc-err" style="display:none"></div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning"><i class="bi bi-check-circle me-1"></i>Enregistrer</button>
                <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
</div></div>
@endsection
@section('scripts')
<script>
// Fonction pour réinitialiser l'erreur du champ (bordure rouge + message sous le champ)
function resetFieldError(fieldId, errId) {
    const field = document.getElementById(fieldId);
    const errDiv = document.getElementById(errId);
    if(field) {
        field.classList.remove('is-invalid');
    }
    if(errDiv) {
        errDiv.style.display = 'none';
        errDiv.textContent = '';
    }
}

// Fonction pour afficher l'erreur du champ
function showFieldError(fieldId, errId, message) {
    const field = document.getElementById(fieldId);
    const errDiv = document.getElementById(errId);
    if(field) {
        field.classList.add('is-invalid');
    }
    if(errDiv) {
        errDiv.textContent = message;
        errDiv.style.display = 'block';
    }
}

// Réinitialiser les erreurs quand l'utilisateur clique ou tape sur le champ nom
const nomInput = document.getElementById('nomSvcE');
if(nomInput) {
    nomInput.addEventListener('input', function() {
        resetFieldError('nomSvcE', 'nomSvcE-err');
    });
    nomInput.addEventListener('click', function() {
        resetFieldError('nomSvcE', 'nomSvcE-err');
    });
    nomInput.addEventListener('focus', function() {
        resetFieldError('nomSvcE', 'nomSvcE-err');
    });
}

// Réinitialiser les erreurs quand l'utilisateur tape sur la description
const descriptionTextarea = document.getElementById('description');
if(descriptionTextarea) {
    descriptionTextarea.addEventListener('input', function() {
        resetFieldError('description', 'desc-err');
        // Mise à jour du compteur
        const len = this.value.length;
        const max = 500;
        const descHint = document.getElementById('descHint');
        if (descHint) {
            if (len > max) {
                descHint.innerHTML = `<span style="color:var(--red)">⚠️ ${len}/${max} caractères - Limite dépassée !</span>`;
            } else {
                descHint.innerHTML = `${len}/${max} caractères`;
                descHint.style.color = 'var(--muted)';
            }
        }
    });
    descriptionTextarea.addEventListener('click', function() {
        resetFieldError('description', 'desc-err');
    });
    descriptionTextarea.addEventListener('focus', function() {
        resetFieldError('description', 'desc-err');
    });
}

// Validation à la soumission
document.getElementById('formSvcE').addEventListener('submit', function(e) {
    let isValid = true;
    const nomInput = document.getElementById('nomSvcE');
    const description = document.getElementById('description');
    const descErr = document.getElementById('desc-err');

    // Reset des erreurs de champ avant validation
    resetFieldError('nomSvcE', 'nomSvcE-err');
    if (descErr) resetFieldError('description', 'desc-err');

    // Validation du nom du service
    const nom = nomInput.value.trim();

    if (!nom) {
        showFieldError('nomSvcE', 'nomSvcE-err', 'Le nom du service est obligatoire.');
        isValid = false;
    } else if (/^\d+$/.test(nom)) {
        showFieldError('nomSvcE', 'nomSvcE-err', 'Le nom du service ne peut pas être composé uniquement de chiffres.');
        isValid = false;
    } else if (!/[A-Za-zÀ-ÿ]/u.test(nom)) {
        showFieldError('nomSvcE', 'nomSvcE-err', 'Le nom du service doit contenir au moins une lettre.');
        isValid = false;
    } else if (!/^[\p{L}0-9\s\-\']+$/u.test(nom)) {
        showFieldError('nomSvcE', 'nomSvcE-err', 'Le nom ne doit contenir que des lettres, chiffres, espaces ou tirets.');
        isValid = false;
    }

    // Validation de la description
    if (description && description.value.length > 500) {
        showFieldError('description', 'desc-err', 'La description ne peut pas dépasser 500 caractères.');
        isValid = false;
    }

    if (!isValid) {
        e.preventDefault();
    }
});

// Compteur de caractères pour la description au chargement
const descTextarea = document.getElementById('description');
if (descTextarea) {
    const descHint = document.getElementById('descHint');
    const initialLen = descTextarea.value.length;
    const max = 500;
    if (descHint) {
        if (initialLen > max) {
            descHint.innerHTML = `<span style="color:var(--red)">⚠️ ${initialLen}/${max} caractères - Limite dépassée !</span>`;
        } else {
            descHint.innerHTML = `${initialLen}/${max} caractères`;
        }
    }
}
</script>
<style>
.invalid-feedback {
    display: none;
}
.is-invalid ~ .invalid-feedback {
    display: block;
}
</style>
@endsection
