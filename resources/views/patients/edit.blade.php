@extends('layouts.app')
@section('title','Modifier — '.$patient->prenom.' '.$patient->nom)
@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-pencil-square" style="color:var(--amber)"></i> Modifier les informations du patient
    </div>
    <div class="card-body p-4">
        <div class="alert alert-secondary mb-4" style="font-size:.83rem">
            <i class="bi bi-upc me-1"></i>
            Code actuel : <strong class="code-badge">{{ $patient->code_unique }}</strong>
            <span style="color:var(--muted);margin-left:8px">— Mis à jour si vous modifiez le NPI ou le téléphone.</span>
        </div>
        @if($errors->any())
        <div class="alert alert-danger mb-4"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ route('patients.update',$patient) }}" id="formEdit" novalidate>
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom',$patient->nom) }}"
                           class="form-control @error('nom') is-invalid @enderror" required autocomplete="off">
                    <div class="invalid-feedback" id="nom-err">{{ $errors->first('nom') ?: 'Lettres uniquement.' }}</div>
                    <div class="field-hint">Lettres, espaces et tirets.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Prénom <span class="text-danger">*</span></label>
                    <input type="text" name="prenom" id="prenom" value="{{ old('prenom',$patient->prenom) }}"
                           class="form-control @error('prenom') is-invalid @enderror" required autocomplete="off">
                    <div class="invalid-feedback" id="prenom-err">{{ $errors->first('prenom') ?: 'Lettres uniquement.' }}</div>
                    <div class="field-hint">Lettres, espaces et tirets.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NPI</label>
                    <input type="text" name="npi" id="npi" value="{{ old('npi',$patient->npi) }}"
                           class="form-control @error('npi') is-invalid @enderror"
                           maxlength="10" inputmode="numeric" autocomplete="off">
                    <div class="invalid-feedback" id="npi-err">{{ $errors->first('npi') ?: 'Exactement 10 chiffres.' }}</div>
                    <div class="field-hint">Modifie le code unique si valide.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="telephone" id="telephone" value="{{ old('telephone',$patient->telephone) }}"
                           class="form-control @error('telephone') is-invalid @enderror"
                           maxlength="15" inputmode="tel" autocomplete="off">
                    <div class="invalid-feedback" id="tel-err">{{ $errors->first('telephone') ?: 'Chiffres uniquement (8-15), + au début.' }}</div>
                    <div class="field-hint">Chiffres et + uniquement.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date de naissance</label>
                    <input type="date" name="date_naissance" id="dob"
                           value="{{ old('date_naissance',$patient->date_naissance?->format('Y-m-d')) }}"
                           class="form-control @error('date_naissance') is-invalid @enderror"
                           max="{{ date('Y-m-d') }}">
                    <div class="invalid-feedback" id="dob-err">{{ $errors->first('date_naissance') ?: 'Date invalide.' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="adresse" id="adresse" value="{{ old('adresse',$patient->adresse) }}"
                           class="form-control @error('adresse') is-invalid @enderror">
                    <div class="invalid-feedback" id="adr-err">{{ $errors->first('adresse') ?: 'Adresse invalide.' }}</div>
                </div>
                <div class="col-12 pt-2 d-flex gap-2">
                    <button type="submit" class="btn btn-warning"><i class="bi bi-check-circle me-1"></i>Enregistrer les modifications</button>
                    <a href="{{ route('patients.show',$patient) }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection

@section('scripts')
<script>
const lettresReg = /^[\p{L}\s\-']+$/u;
const chiffresReg = /^[\+]?[0-9]{8,15}$/;
const npiReg = /^[0-9]{10}$/;
const adrReg = /^[\p{L}0-9\s,.\-']+$/u;

// Fonction pour réinitialiser l'état d'erreur d'un champ
function resetFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    const errEl = document.getElementById(fieldId + '-err');
    if(field) {
        field.classList.remove('is-invalid');
    }
    if(errEl) {
        errEl.style.display = '';
    }
}

// Fonction pour valider un champ (pour le feedback visuel)
function setV(el, ok, err, msg) {
    el.classList.toggle('is-invalid', !ok);
    el.classList.toggle('is-valid', ok && el.value.trim() !== '');
    if (!ok && msg) err.textContent = msg;
}

// Bloquer les chiffres dans nom/prénom en temps réel + réinitialiser erreur
['nom', 'prenom'].forEach(id => {
    const el = document.getElementById(id);
    el.addEventListener('input', function() {
        this.value = this.value.replace(/[0-9]/g, '');
        // Réinitialiser l'erreur dès qu'on tape
        resetFieldError(id);
    });
    el.addEventListener('focus', function() {
        resetFieldError(id);
    });
});

// Bloquer les lettres dans NPI / téléphone + réinitialiser erreur
['npi', 'telephone'].forEach(id => {
    const el = document.getElementById(id);
    if(el) {
        el.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9\+]/g, '');
            // Réinitialiser l'erreur dès qu'on tape
            resetFieldError(id);
        });
        el.addEventListener('focus', function() {
            resetFieldError(id);
        });
    }
});

// Pour la date de naissance - réinitialiser l'erreur
const dobField = document.getElementById('dob');
if(dobField) {
    dobField.addEventListener('input', function() {
        resetFieldError('dob');
    });
    dobField.addEventListener('change', function() {
        resetFieldError('dob');
    });
    dobField.addEventListener('focus', function() {
        resetFieldError('dob');
    });
}

// Pour l'adresse - réinitialiser l'erreur
const adrField = document.getElementById('adresse');
if(adrField) {
    adrField.addEventListener('input', function() {
        resetFieldError('adresse');
    });
    adrField.addEventListener('focus', function() {
        resetFieldError('adresse');
    });
}

// Validation à la soumission
document.getElementById('formEdit').addEventListener('submit', function(e) {
    let ok = true;

    const nom = document.getElementById('nom');
    const pr = document.getElementById('prenom');
    const npi = document.getElementById('npi');
    const tel = document.getElementById('telephone');
    const dob = document.getElementById('dob');
    const adr = document.getElementById('adresse');

    // Réinitialiser toutes les erreurs avant validation
    ['nom', 'prenom', 'npi', 'telephone', 'dob', 'adresse'].forEach(id => {
        resetFieldError(id);
    });

    // Validation du nom
    if (!nom.value.trim() || !lettresReg.test(nom.value)) {
        setV(nom, false, document.getElementById('nom-err'), 'Nom invalide (lettres uniquement).');
        ok = false;
    } else {
        setV(nom, true, document.getElementById('nom-err'), '');
    }

    // Validation du prénom
    if (!pr.value.trim() || !lettresReg.test(pr.value)) {
        setV(pr, false, document.getElementById('prenom-err'), 'Prénom invalide (lettres uniquement).');
        ok = false;
    } else {
        setV(pr, true, document.getElementById('prenom-err'), '');
    }

    // Validation du NPI
    if (npi.value && !npiReg.test(npi.value)) {
        setV(npi, false, document.getElementById('npi-err'), 'NPI : exactement 10 chiffres.');
        ok = false;
    } else if (npi.value && npiReg.test(npi.value)) {
        setV(npi, true, document.getElementById('npi-err'), '');
    }

    // Validation du téléphone
    if (tel.value && !chiffresReg.test(tel.value)) {
        setV(tel, false, document.getElementById('tel-err'), 'Téléphone invalide (8-15 chiffres, + autorisé).');
        ok = false;
    } else if (tel.value && chiffresReg.test(tel.value)) {
        setV(tel, true, document.getElementById('tel-err'), '');
    }

    // Validation de la date de naissance
    if (dob.value) {
        const d = new Date(dob.value);
        if (isNaN(d) || d >= new Date()) {
            setV(dob, false, document.getElementById('dob-err'), 'Date invalide ou dans le futur.');
            ok = false;
        } else {
            setV(dob, true, document.getElementById('dob-err'), '');
        }
    }

    // Validation de l'adresse
    if (adr.value && !adrReg.test(adr.value)) {
        setV(adr, false, document.getElementById('adr-err'), 'Adresse invalide.');
        ok = false;
    } else if (adr.value && adrReg.test(adr.value)) {
        setV(adr, true, document.getElementById('adr-err'), '');
    }

    if (!ok) e.preventDefault();
});
</script>
@endsection
