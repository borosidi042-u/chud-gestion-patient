@extends('layouts.app')
@section('title','Nouveau patient')
@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-person-plus-fill" style="color:var(--blue)"></i> Enregistrement d'un nouveau patient
    </div>
    <div class="card-body p-4">
        <div class="alert alert-info py-2 mb-4" style="font-size:.83rem">
            <i class="bi bi-info-circle me-1"></i>
            <strong>Code unique :</strong> NPI 10 chiffres (priorité 1) → Téléphone 10 chiffres (priorité 2) → Numéro d'ordre automatique.
        </div>
        @if($errors->any())
        <div class="alert alert-danger mb-4"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ route('patients.store') }}" id="formPatient" novalidate>
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom') }}"
                           class="form-control @error('nom') is-invalid @enderror"
                           placeholder="Ex: KORA" autocomplete="off" required>
                    <div class="invalid-feedback" id="nom-err">{{ $errors->first('nom') ?: 'Lettres uniquement (pas de chiffres).' }}</div>
                    <div class="field-hint">Lettres, espaces et tirets uniquement.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Prénom <span class="text-danger">*</span></label>
                    <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}"
                           class="form-control @error('prenom') is-invalid @enderror"
                           placeholder="Ex: Moussa" autocomplete="off" required>
                    <div class="invalid-feedback" id="prenom-err">{{ $errors->first('prenom') ?: 'Lettres uniquement (pas de chiffres).' }}</div>
                    <div class="field-hint">Lettres, espaces et tirets uniquement.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NPI <span class="text-muted fw-normal">(optionnel)</span></label>
                    <input type="text" name="npi" id="npi" value="{{ old('npi') }}"
                           class="form-control @error('npi') is-invalid @enderror"
                           placeholder="Ex: 1234567890" maxlength="10" inputmode="numeric" autocomplete="off">
                    <div class="invalid-feedback" id="npi-err">{{ $errors->first('npi') ?: 'Exactement 10 chiffres (0-9).' }}</div>
                    <div class="field-hint">Exactement 10 chiffres — deviendra le code unique.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Téléphone <span class="text-muted fw-normal">(optionnel)</span></label>
                    <input type="text" name="telephone" id="telephone" value="{{ old('telephone') }}"
                           class="form-control @error('telephone') is-invalid @enderror"
                           placeholder="Ex: 0612345678" maxlength="15" inputmode="tel" autocomplete="off">
                    <div class="invalid-feedback" id="tel-err">{{ $errors->first('telephone') ?: 'Chiffres uniquement (8-15), + autorisé au début.' }}</div>
                    <div class="field-hint">Chiffres et + uniquement.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date de naissance</label>
                    <input type="date" name="date_naissance" id="dob" value="{{ old('date_naissance') }}"
                           class="form-control @error('date_naissance') is-invalid @enderror"
                           max="{{ date('Y-m-d') }}">
                    <div class="invalid-feedback" id="dob-err">{{ $errors->first('date_naissance') ?: 'Date invalide ou dans le futur.' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="adresse" id="adresse" value="{{ old('adresse') }}"
                           class="form-control @error('adresse') is-invalid @enderror"
                           placeholder="Ex: Banikanni, Parakou">
                    <div class="invalid-feedback" id="adr-err">{{ $errors->first('adresse') ?: 'Adresse invalide.' }}</div>
                    <div class="field-hint">Lettres, chiffres, virgules et tirets.</div>
                </div>
                <div class="col-12 pt-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="bi bi-check-circle me-1"></i> Enregistrer le patient
                    </button>
                    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">Annuler</a>
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
// ── Validation frontend patient ──────────────────────────────────────────
const lettresReg = /^[\p{L}\s\-']+$/u;
const chiffresReg = /^[\+]?[0-9]{8,15}$/;
const npiReg      = /^[0-9]{10}$/;
const adrReg      = /^[\p{L}0-9\s,.\-']+$/u;

function setValid(el, ok, errEl, msg) {
    el.classList.toggle('is-invalid', !ok);
    el.classList.toggle('is-valid',   ok && el.value.trim() !== '');
    if (!ok && msg) errEl.textContent = msg;
}

// Bloquer les chiffres dans nom/prénom en temps réel
['nom','prenom'].forEach(id => {
    const el = document.getElementById(id);
    el.addEventListener('input', function() {
        this.value = this.value.replace(/[0-9]/g,'');
        const ok = this.value.trim()==='' || lettresReg.test(this.value);
        setValid(this, ok || this.value.trim()==='', document.getElementById(id+'-err'),
                 'Le '+id+' ne doit contenir que des lettres.');
    });
});

// Bloquer les lettres dans NPI / téléphone
['npi','telephone'].forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9\+]/g,'');
    });
});

// Validation à la soumission
document.getElementById('formPatient').addEventListener('submit', function(e) {
    let valid = true;

    const nom    = document.getElementById('nom');
    const prenom = document.getElementById('prenom');
    const npi    = document.getElementById('npi');
    const tel    = document.getElementById('telephone');
    const dob    = document.getElementById('dob');
    const adr    = document.getElementById('adresse');

    if (!nom.value.trim() || !lettresReg.test(nom.value)) {
        setValid(nom,false,document.getElementById('nom-err'),'Nom invalide (lettres uniquement).');
        valid=false;
    }
    if (!prenom.value.trim() || !lettresReg.test(prenom.value)) {
        setValid(prenom,false,document.getElementById('prenom-err'),'Prénom invalide (lettres uniquement).');
        valid=false;
    }
    if (npi.value && !npiReg.test(npi.value)) {
        setValid(npi,false,document.getElementById('npi-err'),'NPI : exactement 10 chiffres.');
        valid=false;
    }
    if (tel.value && !chiffresReg.test(tel.value)) {
        setValid(tel,false,document.getElementById('tel-err'),'Téléphone : 8 à 15 chiffres, + autorisé.');
        valid=false;
    }
    if (dob.value) {
        const d = new Date(dob.value);
        if (isNaN(d) || d >= new Date()) {
            setValid(dob,false,document.getElementById('dob-err'),'Date invalide ou dans le futur.');
            valid=false;
        }
    }
    if (adr.value && !adrReg.test(adr.value)) {
        setValid(adr,false,document.getElementById('adr-err'),'Adresse invalide.');
        valid=false;
    }
    if (!valid) e.preventDefault();
});
</script>
@endsection
