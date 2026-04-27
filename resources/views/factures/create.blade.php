@extends('layouts.app')
@section('title','Nouvelle facture')
@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-receipt-cutoff" style="color:var(--green)"></i> Enregistrement d'une facture
    </div>
    <div class="card-body p-4">
        @if($errors->any())
        <div class="alert alert-danger mb-4"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ route('factures.store') }}" id="formFacture" novalidate>
            @csrf

            {{-- Selection patient --}}
            @if($patient)
                <div class="alert alert-info mb-4" style="font-size:.85rem">
                    <i class="bi bi-person-check me-1"></i>
                    Patient : <strong>{{ $patient->prenom }} {{ $patient->nom }}</strong>
                    - <span class="code-badge">{{ $patient->code_unique }}</span>
                </div>
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
            @else
                <div class="mb-4">
                    <label class="form-label">Patient <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input type="text" id="patientSearch" class="form-control"
                               placeholder="Tapez nom, prénom, code, NPI ou téléphone..." autocomplete="off">
                        <div id="patientDropdown" class="patient-dropdown" style="display:none"></div>
                    </div>
                    <input type="hidden" name="patient_id" id="patientId" value="{{ old('patient_id') }}">
                    <div id="patientSelected" class="alert alert-info py-2 mt-2" style="display:none;font-size:.83rem"></div>
                    @error('patient_id')<div style="color:var(--red);font-size:.77rem;margin-top:4px">{{ $message }}</div>@enderror
                    <div class="field-hint">Commencez a taper pour filtrer.</div>
                </div>
            @endif

            {{-- Informations facture --}}
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">N° de reçu papier <span class="text-danger">*</span></label>
                    <input type="text" name="numero_facture" id="numF" value="{{ old('numero_facture') }}"
                           class="form-control @error('numero_facture') is-invalid @enderror"
                           placeholder="Ex: 151F26-001015" required>
                    <div class="invalid-feedback" id="numF-err">{{ $errors->first('numero_facture') ?: 'Format invalide.' }}</div>
                    <div class="field-hint">Lettres, chiffres, tirets, slashes.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date de la facture <span class="text-danger">*</span></label>
                    <input type="date" name="date_facture" id="dateF" value="{{ old('date_facture',date('Y-m-d')) }}"
                           class="form-control @error('date_facture') is-invalid @enderror"
                           max="{{ date('Y-m-d') }}" required>
                    <div class="invalid-feedback">{{ $errors->first('date_facture') ?: 'Date invalide.' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Service concerné <span class="text-danger">*</span></label>
                    <select name="service_id" id="svcF" class="form-select @error('service_id') is-invalid @enderror" required>
                        <option value="">- Choisir un service -</option>
                        @foreach($services as $s)
                        <option value="{{ $s->id }}" {{ old('service_id')==$s->id?'selected':'' }}>{{ $s->nom_service }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">{{ $errors->first('service_id') ?: 'Choisir un service.' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Montant total (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" name="montant" id="montantF" value="{{ old('montant') }}"
                           class="form-control @error('montant') is-invalid @enderror"
                           placeholder="Ex: 12000" min="1" step="1" required>
                    <div class="invalid-feedback" id="mntF-err">{{ $errors->first('montant') ?: 'Montant invalide.' }}</div>
                    <div class="field-hint">Montant total inscrit sur le reçu.</div>
                </div>
            </div>

            {{-- Section Prise en charge (facultative) --}}
            <div class="mt-4 p-3" style="background:#F8FAFD;border-radius:10px;border:1.5px dashed #DDE3EC">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="pecToggle"
                               {{ old('pec_organisme') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="pecToggle" style="font-size:.88rem">
                            <i class="bi bi-shield-fill-check me-1" style="color:var(--green)"></i>
                            Prise en charge par un organisme ?
                        </label>
                    </div>
                    <span style="font-size:.75rem;color:var(--muted)">(facultatif)</span>
                </div>

                <div id="pecFields" style="{{ old('pec_organisme') ? '' : 'display:none' }}">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label">Nom de l'organisme</label>
                            <input type="text" name="pec_organisme" id="pecOrg" value="{{ old('pec_organisme') }}"
                                   class="form-control @error('pec_organisme') is-invalid @enderror"
                                   placeholder="Ex: Min Ens Sec et de la Fo, CNSS, RAMU...">
                            <div class="invalid-feedback">{{ $errors->first('pec_organisme') }}</div>
                            <div class="field-hint">Ministère, assurance, mutuelle...</div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Montant pris en charge (FCFA)</label>
                            <input type="number" name="pec_montant" id="pecMnt" value="{{ old('pec_montant') }}"
                                   class="form-control @error('pec_montant') is-invalid @enderror"
                                   placeholder="Ex: 2400" min="0" step="1" oninput="calcReste()">
                            <div class="invalid-feedback" id="pecMnt-err">{{ $errors->first('pec_montant') ?: 'Montant invalide.' }}</div>
                        </div>
                        <div class="col-12">
                            {{-- Calcul automatique du reste a charge --}}
                            <div id="resteBox" style="display:none;background:var(--blue-l);border-radius:8px;padding:10px 14px">
                                <div class="d-flex justify-content-between" style="font-size:.85rem">
                                    <span style="color:var(--muted)">Montant total :</span>
                                    <span class="fw-semibold" id="dispTotal">-</span>
                                </div>
                                <div class="d-flex justify-content-between" style="font-size:.85rem;margin-top:4px">
                                    <span style="color:var(--green)">Prise en charge :</span>
                                    <span class="fw-semibold" style="color:var(--green)" id="dispPEC">-</span>
                                </div>
                                <div class="d-flex justify-content-between" style="font-size:.9rem;margin-top:6px;padding-top:6px;border-top:1px solid rgba(0,90,156,.15)">
                                    <span class="fw-semibold" style="color:var(--blue-d)">Solde patient :</span>
                                    <span class="fw-bold" style="color:var(--blue-d);font-size:1rem" id="dispReste">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 pt-3 d-flex gap-2">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i>Enregistrer la facture</button>
                <a href="{{ $patient ? route('patients.show',$patient) : route('factures.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection

@section('scripts')
<script>
// Toggle section prise en charge
var pecToggle = document.getElementById('pecToggle');
var pecFields = document.getElementById('pecFields');
if (pecToggle && pecFields) {
    pecToggle.addEventListener('change', function() {
        if (this.checked) {
            pecFields.style.display = 'block';
        } else {
            pecFields.style.display = 'none';
            var pecOrg = document.getElementById('pecOrg');
            var pecMnt = document.getElementById('pecMnt');
            var resteBox = document.getElementById('resteBox');
            if (pecOrg) pecOrg.value = '';
            if (pecMnt) pecMnt.value = '';
            if (resteBox) resteBox.style.display = 'none';
        }
    });
}

// Calcul automatique reste a charge
function calcReste() {
    var montantInput = document.getElementById('montantF');
    var pecInput = document.getElementById('pecMnt');
    var resteBox = document.getElementById('resteBox');

    if (!montantInput || !pecInput || !resteBox) return;

    var total = parseFloat(montantInput.value) || 0;
    var pec = parseFloat(pecInput.value) || 0;
    var reste = total - pec;

    if (total > 0 && pec >= 0) {
        resteBox.style.display = 'block';
        var dispTotal = document.getElementById('dispTotal');
        var dispPEC = document.getElementById('dispPEC');
        var dispReste = document.getElementById('dispReste');

        if (dispTotal) dispTotal.textContent = total.toLocaleString('fr-FR') + ' FCFA';
        if (dispPEC) dispPEC.textContent = pec.toLocaleString('fr-FR') + ' FCFA';
        if (dispReste) dispReste.textContent = reste.toLocaleString('fr-FR') + ' FCFA';
        resteBox.style.borderLeft = reste < 0 ? '3px solid var(--red)' : '3px solid var(--blue)';
    } else {
        resteBox.style.display = 'none';
    }
}

var montantField = document.getElementById('montantF');
if (montantField) {
    montantField.addEventListener('input', calcReste);
}

// Filtrer patient
@if(!$patient)
    var allPatients = {!! json_encode(\App\Models\Patient::select('id','nom','prenom','code_unique','npi','telephone')->get()) !!};
    var patientSearch = document.getElementById('patientSearch');
    var patientDropdown = document.getElementById('patientDropdown');
    var patientIdInput = document.getElementById('patientId');
    var patientSelectedDiv = document.getElementById('patientSelected');

    if (patientSearch && patientDropdown) {
        patientSearch.addEventListener('input', function() {
            var q = this.value.toLowerCase().trim();
            patientDropdown.innerHTML = '';

            if (q.length < 1) {
                patientDropdown.style.display = 'none';
                return;
            }

            var results = allPatients.filter(function(p) {
                return (p.nom || '').toLowerCase().includes(q) ||
                       (p.prenom || '').toLowerCase().includes(q) ||
                       (p.code_unique || '').includes(q) ||
                       (p.npi || '').includes(q) ||
                       (p.telephone || '').includes(q);
            }).slice(0, 8);

            if (results.length === 0) {
                patientDropdown.innerHTML = '<div class="patient-dropdown-item" style="color:var(--muted)">Aucun résultat</div>';
            } else {
                for (var i = 0; i < results.length; i++) {
                    var p = results[i];
                    var div = document.createElement('div');
                    div.className = 'patient-dropdown-item';
                    div.innerHTML = '<strong>' + p.prenom + ' ' + p.nom + '</strong> <span style="color:var(--muted);font-size:.78rem">- ' + p.code_unique + '</span>';
                    div.onclick = (function(patient) {
                        return function() {
                            if (patientIdInput) patientIdInput.value = patient.id;
                            if (patientSearch) patientSearch.value = patient.prenom + ' ' + patient.nom;
                            if (patientDropdown) patientDropdown.style.display = 'none';
                            if (patientSelectedDiv) {
                                patientSelectedDiv.style.display = 'block';
                                patientSelectedDiv.innerHTML = '<i class="bi bi-person-check me-1"></i>Patient : <strong>' + patient.prenom + ' ' + patient.nom + '</strong> - <span class="code-badge">' + patient.code_unique + '</span>';
                            }
                        };
                    })(p);
                    patientDropdown.appendChild(div);
                }
            }
            patientDropdown.style.display = 'block';
        });

        document.addEventListener('click', function(e) {
            if (patientSearch && patientDropdown && !patientSearch.contains(e.target) && !patientDropdown.contains(e.target)) {
                patientDropdown.style.display = 'none';
            }
        });
    }
@endif

// Validation soumission
var numReg = /^[A-Za-z0-9\-\/]+$/;

function validateField(el, ok, eid, msg) {
    if (el) {
        if (ok) {
            el.classList.remove('is-invalid');
        } else {
            el.classList.add('is-invalid');
        }
    }
    var errEl = document.getElementById(eid);
    if (errEl && !ok) {
        errEl.textContent = msg;
    }
}

var formFacture = document.getElementById('formFacture');
if (formFacture) {
    formFacture.addEventListener('submit', function(e) {
        var ok = true;

        @if(!$patient)
        var patientId = document.getElementById('patientId');
        if (!patientId || !patientId.value) {
            alert('Veuillez sélectionner un patient.');
            ok = false;
        }
        @endif

        var numF = document.getElementById('numF');
        if (numF && (!numF.value.trim() || !numReg.test(numF.value.trim()))) {
            validateField(numF, false, 'numF-err', 'N° de reçu invalide.');
            ok = false;
        }

        var montant = document.getElementById('montantF');
        if (montant && (!montant.value || parseFloat(montant.value) <= 0)) {
            validateField(montant, false, 'mntF-err', 'Montant doit etre > 0.');
            ok = false;
        }

        var dateF = document.getElementById('dateF');
        if (dateF && (!dateF.value || new Date(dateF.value) > new Date())) {
            validateField(dateF, false, '', 'Date invalide.');
            ok = false;
        }

        var service = document.getElementById('svcF');
        if (service && !service.value) {
            service.classList.add('is-invalid');
            ok = false;
        }

        var pecToggleCheck = document.getElementById('pecToggle');
        if (pecToggleCheck && pecToggleCheck.checked) {
            var pecMnt = document.getElementById('pecMnt');
            var montantTotal = document.getElementById('montantF');
            if (pecMnt && pecMnt.value && montantTotal && parseFloat(pecMnt.value) > parseFloat(montantTotal.value)) {
                validateField(pecMnt, false, 'pecMnt-err', 'La PEC ne peut pas depasser le montant total.');
                ok = false;
            }
        }

        if (!ok) e.preventDefault();
    });
}

// Init si valeurs old() presentes
var pecMntInit = document.getElementById('pecMnt');
if (pecMntInit && pecMntInit.value) {
    calcReste();
}
</script>
@endsection
