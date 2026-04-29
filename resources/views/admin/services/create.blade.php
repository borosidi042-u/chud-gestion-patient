@extends('layouts.app')
@section('title','Nouveau service')
@section('content')
<div class="row justify-content-center"><div class="col-lg-5">
<div class="card">
    <div class="card-header"><i class="bi bi-building-fill me-2" style="color:var(--blue)"></i>Ajouter un service</div>
    <div class="card-body p-4">
        @if($errors->any())
        <div class="alert alert-danger mb-3"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ route('admin.services.store') }}" id="formSvc" novalidate>
            @csrf
            <div class="mb-3">
                <label class="form-label">Nom du service <span class="text-danger">*</span></label>
                <input type="text" name="nom_service" id="nomSvc" value="{{ old('nom_service') }}"
                       class="form-control @error('nom_service') is-invalid @enderror"
                       placeholder="Ex: Radiologie, Urgences, Labo01..." required>
                <div class="invalid-feedback" id="nomSvc-err">{{ $errors->first('nom_service') ?: 'Nom invalide.' }}</div>
                <div class="field-hint">Lettres, chiffres, espaces et tirets. Ne peut pas être uniquement des chiffres.</div>
            </div>
            <div class="mb-4">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" id="description"
                          placeholder="Description optionnelle (tous caractères autorisés)">{{ old('description') }}</textarea>
                <div class="field-hint" id="descHint">Maximum 500 caractères.</div>
                <div class="invalid-feedback" id="desc-err" style="display:none"></div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Ajouter</button>
                <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
</div></div>
@endsection
@section('scripts')
<script>
document.getElementById('formSvc').addEventListener('submit', function(e) {
    let isValid = true;
    const nomInput = document.getElementById('nomSvc');
    const nomErr = document.getElementById('nomSvc-err');
    const description = document.getElementById('description');
    const descErr = document.getElementById('desc-err');

    // Reset styles
    nomInput.classList.remove('is-invalid');
    nomErr.style.display = 'none';
    if (descErr) descErr.style.display = 'none';
    description.classList.remove('is-invalid');

    // Validation du nom du service
    const nom = nomInput.value.trim();

    if (!nom) {
        nomInput.classList.add('is-invalid');
        nomErr.textContent = 'Le nom du service est obligatoire.';
        nomErr.style.display = 'block';
        isValid = false;
    } else if (/^\d+$/.test(nom)) {
        // Uniquement des chiffres
        nomInput.classList.add('is-invalid');
        nomErr.textContent = 'Le nom du service ne peut pas être composé uniquement de chiffres.';
        nomErr.style.display = 'block';
        isValid = false;
    } else if (!/[A-Za-zÀ-ÿ]/u.test(nom)) {
        // Aucune lettre
        nomInput.classList.add('is-invalid');
        nomErr.textContent = 'Le nom du service doit contenir au moins une lettre.';
        nomErr.style.display = 'block';
        isValid = false;
    } else if (!/^[\p{L}0-9\s\-\']+$/u.test(nom)) {
        // Caractères non autorisés
        nomInput.classList.add('is-invalid');
        nomErr.textContent = 'Le nom ne doit contenir que des lettres, chiffres, espaces ou tirets.';
        nomErr.style.display = 'block';
        isValid = false;
    }

    // Validation de la description (optionnelle mais vérifier longueur)
    if (description && description.value.length > 500) {
        description.classList.add('is-invalid');
        if (descErr) {
            descErr.textContent = 'La description ne peut pas dépasser 500 caractères.';
            descErr.style.display = 'block';
        }
        isValid = false;
    }

    if (!isValid) {
        e.preventDefault();
    }
});

// Compteur de caractères pour la description
const descTextarea = document.getElementById('description');
if (descTextarea) {
    const descHint = document.getElementById('descHint');
    descTextarea.addEventListener('input', function() {
        const len = this.value.length;
        const max = 500;
        if (descHint) {
            if (len > max) {
                descHint.innerHTML = `<span style="color:var(--red)">⚠️ ${len}/${max} caractères - Limite dépassée !</span>`;
                descHint.style.color = 'var(--red)';
            } else {
                descHint.innerHTML = `${len}/${max} caractères`;
                descHint.style.color = 'var(--muted)';
            }
        }
    });
}
</script>
@endsection
