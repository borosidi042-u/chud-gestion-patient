@extends('layouts.app')
@section('title', 'Nouveau mouvement')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-diagram-3-fill me-2"></i> Enregistrement d'un mouvement
                </div>
                <div class="card-body">

                    @if(isset($patient) && $patient)
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-person-badge me-2"></i>
                            <strong>Patient :</strong> {{ $patient->prenom }} {{ $patient->nom }}
                            <span class="code-badge ms-2">{{ $patient->code_unique }}</span>
                        </div>
                    @endif

                    @if(isset($visiteEnCours) && $visiteEnCours)
                        <div class="alert alert-success mb-4">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Une visite est en cours depuis le {{ $visiteEnCours->date_debut->format('d/m/Y à H:i') }}
                        </div>
                    @endif

                    @php
                        $activeTab = request()->input('tab', 'passage');
                    @endphp

                    {{-- Onglets Bootstrap --}}
                    <ul class="nav nav-tabs mb-4" id="mouvementTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab == 'passage' ? 'active' : '' }}" id="passage-tab" data-bs-toggle="tab" data-bs-target="#passage" type="button" role="tab">
                                <i class="bi bi-arrow-right me-1"></i> Passage sans lit
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab == 'admission' ? 'active' : '' }}" id="admission-tab" data-bs-toggle="tab" data-bs-target="#admission" type="button" role="tab">
                                <i class="bi bi-hospital me-1"></i> Admission avec lit
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab == 'transfert' ? 'active' : '' }}" id="transfert-tab" data-bs-toggle="tab" data-bs-target="#transfert" type="button" role="tab">
                                <i class="bi bi-arrow-left-right me-1"></i> Transfert de service
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab == 'sortie' ? 'active' : '' }}" id="sortie-tab" data-bs-toggle="tab" data-bs-target="#sortie" type="button" role="tab">
                                <i class="bi bi-door-closed me-1"></i> Valider la sortie
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="mouvementTabContent">

                        {{-- Onglet 1 : Passage sans lit --}}
                        <div class="tab-pane fade {{ $activeTab == 'passage' ? 'show active' : '' }}" id="passage" role="tabpanel">
                            <form method="POST" action="{{ route('mouvements.passage') }}" id="formPassage" novalidate>
                                @csrf

                                @if(!isset($patient) || !$patient)
                                <div class="mb-3">
                                    <label class="form-label">Patient <span class="text-danger">*</span></label>
                                    <input type="text" id="searchPatientPassage" class="form-control" placeholder="Tapez le code, nom ou prénom..." autocomplete="off">
                                    <input type="hidden" name="patient_id" id="patientIdPassage">
                                    <div id="selectedPatientPassage" class="alert alert-info mt-2 d-none"></div>
                                    <div class="invalid-feedback" id="patientPassageErr">Veuillez sélectionner un patient valide.</div>
                                    @error('patient_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                @else
                                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                                @endif

                                <div class="mb-3">
                                    <label class="form-label">Service <span class="text-danger">*</span></label>
                                    <select name="service_id" class="form-select" required>
                                        <option value="">-- Sélectionner un service --</option>
                                        @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->nom_service }}</option>
                                        @endforeach
                                    </select>
                                    @error('service_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Note (optionnelle)</label>
                                    <textarea name="note" class="form-control" rows="3" placeholder="Informations complémentaires..."></textarea>
                                    @error('note')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i> Enregistrer le passage
                                </button>
                            </form>
                        </div>

                        {{-- Onglet 2 : Admission avec lit --}}
                        <div class="tab-pane fade {{ $activeTab == 'admission' ? 'show active' : '' }}" id="admission" role="tabpanel">
                            <form method="POST" action="{{ route('mouvements.admission') }}" id="formAdmission" novalidate>
                                @csrf

                                @if(!isset($patient) || !$patient)
                                <div class="mb-3">
                                    <label class="form-label">Patient <span class="text-danger">*</span></label>
                                    <input type="text" id="searchPatientAdmission" class="form-control" placeholder="Tapez le code, nom ou prénom..." autocomplete="off">
                                    <input type="hidden" name="patient_id" id="patientIdAdmission">
                                    <div id="selectedPatientAdmission" class="alert alert-info mt-2 d-none"></div>
                                    <div class="invalid-feedback" id="patientAdmissionErr">Veuillez sélectionner un patient valide.</div>
                                    @error('patient_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                @else
                                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                                @endif

                                <div class="mb-3">
                                    <label class="form-label">Service <span class="text-danger">*</span></label>
                                    <select name="service_id" id="serviceAdmission" class="form-select" required>
                                        <option value="">-- Sélectionner un service --</option>
                                        @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->nom_service }}</option>
                                        @endforeach
                                    </select>
                                    @error('service_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Lit <span class="text-danger">*</span></label>
                                    <select name="lit_id" id="litAdmission" class="form-select" required disabled>
                                        <option value="">-- Choisissez d'abord un service --</option>
                                    </select>
                                    @error('lit_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Note (optionnelle)</label>
                                    <textarea name="note" class="form-control" rows="3" placeholder="Informations complémentaires..."></textarea>
                                    @error('note')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i> Enregistrer l'admission
                                </button>
                            </form>
                        </div>

                        {{-- Onglet 3 : Transfert de service --}}
                        <div class="tab-pane fade {{ $activeTab == 'transfert' ? 'show active' : '' }}" id="transfert" role="tabpanel">
                            <form method="POST" action="{{ route('mouvements.transfert') }}" id="formTransfert" novalidate>
                                @csrf

                                @if(!isset($patient) || !$patient)
                                <div class="mb-3">
                                    <label class="form-label">Patient <span class="text-danger">*</span></label>
                                    <input type="text" id="searchPatientTransfert" class="form-control" placeholder="Tapez le code, nom ou prénom..." autocomplete="off">
                                    <input type="hidden" name="patient_id" id="patientIdTransfert">
                                    <div id="selectedPatientTransfert" class="alert alert-info mt-2 d-none"></div>
                                    <div class="invalid-feedback" id="patientTransfertErr">Veuillez sélectionner un patient valide.</div>
                                    @error('patient_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                @else
                                <input type="hidden" name="patient_id" id="patientIdTransfertHidden" value="{{ $patient->id }}">
                                <div class="alert alert-info mb-3">
                                    <i class="bi bi-person-badge me-2"></i>
                                    Patient : <strong>{{ $patient->prenom }} {{ $patient->nom }}</strong> (Code: {{ $patient->code_unique }})
                                </div>
                                @endif

                                <input type="hidden" name="visite_id" id="visiteIdTransfert" value="{{ isset($visiteEnCours) && $visiteEnCours ? $visiteEnCours->id : '' }}">

                                <div id="infosTransfert" class="alert alert-secondary mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <span id="infoMessageTransfert">Sélectionnez un patient pour voir sa situation actuelle.</span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nouveau service <span class="text-danger">*</span></label>
                                    <select name="nouveau_service_id" id="serviceTransfert" class="form-select" required disabled>
                                        <option value="">-- Sélectionner un service --</option>
                                        @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->nom_service }}</option>
                                        @endforeach
                                    </select>
                                    @error('nouveau_service_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nouveau lit <span class="text-danger">*</span></label>
                                    <select name="nouveau_lit_id" id="litTransfert" class="form-select" required disabled>
                                        <option value="">-- Choisissez d'abord un service --</option>
                                    </select>
                                    @error('nouveau_lit_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Note (optionnelle)</label>
                                    <textarea name="note" class="form-control" rows="3" placeholder="Motif du transfert..."></textarea>
                                    @error('note')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <button type="submit" class="btn btn-warning" id="btnTransfertSubmit" disabled>
                                    <i class="bi bi-arrow-left-right me-1"></i> Effectuer le transfert
                                </button>
                            </form>
                        </div>

                        {{-- Onglet 4 : Valider la sortie --}}
                        <div class="tab-pane fade {{ $activeTab == 'sortie' ? 'show active' : '' }}" id="sortie" role="tabpanel">
                            <form method="POST" action="{{ route('mouvements.sortie') }}" id="formSortie" novalidate>
                                @csrf

                                @if(!isset($patient) || !$patient)
                                <div class="mb-3">
                                    <label class="form-label">Patient <span class="text-danger">*</span></label>
                                    <input type="text" id="searchPatientSortie" class="form-control" placeholder="Tapez le code, nom ou prénom..." autocomplete="off">
                                    <input type="hidden" name="patient_id" id="patientIdSortie">
                                    <div id="selectedPatientSortie" class="alert alert-info mt-2 d-none"></div>
                                    <div class="invalid-feedback" id="patientSortieErr">Veuillez sélectionner un patient valide.</div>
                                    @error('patient_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                @else
                                <input type="hidden" name="patient_id" id="patientIdSortieHidden" value="{{ $patient->id }}">
                                <div class="alert alert-info mb-3">
                                    <i class="bi bi-person-badge me-2"></i>
                                    Patient : <strong>{{ $patient->prenom }} {{ $patient->nom }}</strong> (Code: {{ $patient->code_unique }})
                                </div>
                                @endif

                                <input type="hidden" name="visite_id" id="visiteIdSortie" value="{{ isset($visiteEnCours) && $visiteEnCours ? $visiteEnCours->id : '' }}">

                                <div id="infosSortie" class="alert alert-secondary mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <span id="infoMessageSortie">Sélectionnez un patient pour voir sa situation actuelle.</span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Note (optionnelle)</label>
                                    <textarea name="note" class="form-control" rows="3" placeholder="Informations de sortie..."></textarea>
                                    @error('note')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <button type="submit" class="btn btn-danger" id="btnSortie" disabled>
                                    <i class="bi bi-door-closed me-1"></i> Confirmer la sortie
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Liste des patients pour la recherche
var patientsData = {!! json_encode(\App\Models\Patient::select('id', 'code_unique', 'nom', 'prenom')->get()->toArray()) !!};

// Fonction pour réinitialiser l'erreur patient
function resetPatientError(inputId, hiddenId, errId) {
    var input = document.getElementById(inputId);
    var errDiv = document.getElementById(errId);
    if(input) input.classList.remove('is-invalid');
    if(errDiv) errDiv.style.display = 'none';
}

// Fonction pour afficher l'erreur patient
function showPatientError(inputId, errId) {
    var input = document.getElementById(inputId);
    var errDiv = document.getElementById(errId);
    if(input) input.classList.add('is-invalid');
    if(errDiv) errDiv.style.display = 'block';
}

// Fonction de recherche patient
function setupPatientSearch(inputId, hiddenId, selectedDivId, errId, callback) {
    var input = document.getElementById(inputId);
    if (!input) return;

    var datalistId = inputId + 'Datalist';
    var datalist = document.getElementById(datalistId);
    if (!datalist) {
        datalist = document.createElement('datalist');
        datalist.id = datalistId;
        document.body.appendChild(datalist);
        input.setAttribute('list', datalistId);
    }

    function updateDatalist(searchTerm) {
        datalist.innerHTML = '';
        var filtered = patientsData.filter(function(p) {
            return p.code_unique.includes(searchTerm) ||
                p.nom.toLowerCase().includes(searchTerm.toLowerCase()) ||
                p.prenom.toLowerCase().includes(searchTerm.toLowerCase());
        }).slice(0, 10);

        for (var i = 0; i < filtered.length; i++) {
            var p = filtered[i];
            var option = document.createElement('option');
            option.value = p.code_unique + ' - ' + p.prenom + ' ' + p.nom;
            option.dataset.id = p.id;
            datalist.appendChild(option);
        }
    }

    input.addEventListener('input', function() {
        updateDatalist(this.value);
        resetPatientError(inputId, hiddenId, errId);
    });

    input.addEventListener('focus', function() {
        resetPatientError(inputId, hiddenId, errId);
    });

    input.addEventListener('change', function() {
        var selected = null;
        for (var i = 0; i < patientsData.length; i++) {
            var p = patientsData[i];
            if ((p.code_unique + ' - ' + p.prenom + ' ' + p.nom) === this.value) {
                selected = p;
                break;
            }
        }
        if (selected) {
            document.getElementById(hiddenId).value = selected.id;
            var selectedDiv = document.getElementById(selectedDivId);
            selectedDiv.innerHTML = '<i class="bi bi-person-check me-1"></i> Patient sélectionné : <strong>' + selected.prenom + ' ' + selected.nom + '</strong> (Code: ' + selected.code_unique + ')';
            selectedDiv.classList.remove('d-none');
            resetPatientError(inputId, hiddenId, errId);
            if (callback) callback(selected.id);
        } else if (this.value.trim() !== '') {
            document.getElementById(hiddenId).value = '';
            document.getElementById(selectedDivId).classList.add('d-none');
            showPatientError(inputId, errId);
            if (callback) callback(null);
        }
    });
}

// Chargement des lits disponibles
function fetchLits(serviceId, targetSelectId) {
    var select = document.getElementById(targetSelectId);
    select.innerHTML = '<option value="">Chargement...</option>';
    select.disabled = true;

    fetch('/circuits/lits-disponibles?service_id=' + serviceId)
        .then(function(response) { return response.json(); })
        .then(function(lits) {
            select.innerHTML = '<option value="">-- Choisir un lit --</option>';
            if (lits.length === 0) {
                select.innerHTML += '<option value="" disabled>Aucun lit disponible dans ce service</option>';
            } else {
                for (var i = 0; i < lits.length; i++) {
                    var lit = lits[i];
                    select.innerHTML += '<option value="' + lit.id + '">Lit N°' + lit.numero + ' (Salle: ' + lit.salle.nom + ')</option>';
                }
            }
            select.disabled = false;
        })
        .catch(function(error) {
            console.error('Erreur:', error);
            select.innerHTML = '<option value="">Erreur de chargement</option>';
        });
}

// Fonctions spécifiques pour transfert
function loadPatientForTransfert(patientId) {
    var infoDiv = document.getElementById('infoMessageTransfert');
    var serviceSelect = document.getElementById('serviceTransfert');
    var litSelect = document.getElementById('litTransfert');
    var btnSubmit = document.getElementById('btnTransfertSubmit');
    var visiteInput = document.getElementById('visiteIdTransfert');

    if (!patientId) {
        if (infoDiv) infoDiv.innerHTML = 'Sélectionnez un patient pour voir sa situation actuelle.';
        if (serviceSelect) { serviceSelect.disabled = true; serviceSelect.value = ''; }
        if (litSelect) { litSelect.innerHTML = '<option value="">-- Choisissez d\'abord un service --</option>'; litSelect.disabled = true; }
        if (btnSubmit) btnSubmit.disabled = true;
        if (visiteInput) visiteInput.value = '';
        return;
    }

    fetch('/api/patients/' + patientId)
        .then(function(response) {
            if (!response.ok) throw new Error('Patient non trouvé');
            return response.json();
        })
        .then(function(data) {
            if (data.visite_en_cours) {
                var dateDebut = new Date(data.visite_en_cours.date_debut);
                infoDiv.innerHTML = '<strong>Visite en cours depuis le ' + dateDebut.toLocaleString() + '</strong><br>' +
                    'Service actuel: ' + (data.service_actuel || 'Non déterminé') + '<br>' +
                    'Lit actuel: ' + (data.lit_actuel ? 'N°' + data.lit_actuel.numero : 'Aucun');
                visiteInput.value = data.visite_en_cours.id;
                serviceSelect.disabled = false;
                btnSubmit.disabled = false;
            } else {
                infoDiv.innerHTML = '<strong>Aucune visite en cours</strong><br>Ce patient n\'a pas de visite active. Pour effectuer un transfert, une visite doit être en cours.';
                visiteInput.value = '';
                serviceSelect.disabled = true;
                serviceSelect.value = '';
                litSelect.innerHTML = '<option value="">-- Choisissez d\'abord un service --</option>';
                litSelect.disabled = true;
                btnSubmit.disabled = true;
            }
        })
        .catch(function(error) {
            console.error('Erreur:', error);
            if (infoDiv) infoDiv.innerHTML = '<strong>Erreur</strong><br>Impossible de charger les informations du patient.';
        });
}

// Fonctions spécifiques pour sortie
function loadPatientForSortie(patientId) {
    var infoDiv = document.getElementById('infoMessageSortie');
    var btnSubmit = document.getElementById('btnSortie');
    var visiteInput = document.getElementById('visiteIdSortie');

    if (!patientId) {
        if (infoDiv) infoDiv.innerHTML = 'Sélectionnez un patient pour voir sa situation actuelle.';
        if (btnSubmit) btnSubmit.disabled = true;
        if (visiteInput) visiteInput.value = '';
        return;
    }

    fetch('/api/patients/' + patientId)
        .then(function(response) {
            if (!response.ok) throw new Error('Patient non trouvé');
            return response.json();
        })
        .then(function(data) {
            if (data.visite_en_cours) {
                var dateDebut = new Date(data.visite_en_cours.date_debut);
                infoDiv.innerHTML = '<strong>Visite N°' + data.visite_en_cours.numero_visite + ' en cours</strong><br>' +
                    'Début: ' + dateDebut.toLocaleString() + '<br>' +
                    'Lit actuel: ' + (data.lit_actuel ? 'N°' + data.lit_actuel.numero : 'Aucun');
                visiteInput.value = data.visite_en_cours.id;
                btnSubmit.disabled = false;
            } else {
                infoDiv.innerHTML = '<strong>Aucune visite en cours</strong><br>Ce patient n\'a pas de visite active à clôturer.';
                visiteInput.value = '';
                btnSubmit.disabled = true;
            }
        })
        .catch(function(error) {
            console.error('Erreur:', error);
            if (infoDiv) infoDiv.innerHTML = '<strong>Erreur</strong><br>Impossible de charger les informations du patient.';
        });
}

// Initialisation des recherches patient (sans validation automatique)
setupPatientSearch('searchPatientPassage', 'patientIdPassage', 'selectedPatientPassage', 'patientPassageErr');
setupPatientSearch('searchPatientAdmission', 'patientIdAdmission', 'selectedPatientAdmission', 'patientAdmissionErr');
setupPatientSearch('searchPatientTransfert', 'patientIdTransfert', 'selectedPatientTransfert', 'patientTransfertErr', loadPatientForTransfert);
setupPatientSearch('searchPatientSortie', 'patientIdSortie', 'selectedPatientSortie', 'patientSortieErr', loadPatientForSortie);

// Si un patient est déjà pré-sélectionné
if (document.getElementById('patientIdTransfertHidden')) {
    var preSelectedPatientId = document.getElementById('patientIdTransfertHidden').value;
    if (preSelectedPatientId) loadPatientForTransfert(preSelectedPatientId);
}

if (document.getElementById('patientIdSortieHidden')) {
    var preSelectedPatientIdSortie = document.getElementById('patientIdSortieHidden').value;
    if (preSelectedPatientIdSortie) loadPatientForSortie(preSelectedPatientIdSortie);
}

// Admission: chargement des lits au changement de service
var serviceAdmission = document.getElementById('serviceAdmission');
if (serviceAdmission) {
    serviceAdmission.addEventListener('change', function() {
        if (this.value) {
            fetchLits(this.value, 'litAdmission');
        } else {
            var select = document.getElementById('litAdmission');
            select.innerHTML = '<option value="">-- Choisissez d\'abord un service --</option>';
            select.disabled = true;
        }
    });
}

// Transfert: chargement des lits au changement de service
var serviceTransfert = document.getElementById('serviceTransfert');
if (serviceTransfert) {
    serviceTransfert.addEventListener('change', function() {
        if (this.value) {
            fetchLits(this.value, 'litTransfert');
        } else {
            var select = document.getElementById('litTransfert');
            select.innerHTML = '<option value="">-- Choisissez d\'abord un service --</option>';
            select.disabled = true;
        }
    });
}
</script>

@if(!isset($patient) || !$patient)
<style>
    input[list]::-webkit-calendar-picker-indicator {
        display: none;
    }
    .invalid-feedback {
        display: none;
    }
    .is-invalid ~ .invalid-feedback {
        display: block;
    }
</style>
@endif

@endsection
