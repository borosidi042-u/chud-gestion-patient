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
                            <form method="POST" action="{{ route('mouvements.passage') }}" id="formPassage">
                                @csrf

                                @if(!isset($patient) || !$patient)
                                <div class="mb-3">
                                    <label class="form-label">Patient <span class="text-danger">*</span></label>
                                    <input type="text" id="searchPatientPassage" class="form-control" placeholder="Tapez le code, nom ou prénom...">
                                    <input type="hidden" name="patient_id" id="patientIdPassage">
                                    <div id="selectedPatientPassage" class="alert alert-info mt-2 d-none"></div>
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
                            <form method="POST" action="{{ route('mouvements.admission') }}" id="formAdmission">
                                @csrf

                                @if(!isset($patient) || !$patient)
                                <div class="mb-3">
                                    <label class="form-label">Patient <span class="text-danger">*</span></label>
                                    <input type="text" id="searchPatientAdmission" class="form-control" placeholder="Tapez le code, nom ou prénom...">
                                    <input type="hidden" name="patient_id" id="patientIdAdmission">
                                    <div id="selectedPatientAdmission" class="alert alert-info mt-2 d-none"></div>
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
                            <form method="POST" action="{{ route('mouvements.transfert') }}" id="formTransfert">
                                @csrf

                                @if(!isset($patient) || !$patient)
                                <div class="mb-3">
                                    <label class="form-label">Patient <span class="text-danger">*</span></label>
                                    <input type="text" id="searchPatientTransfert" class="form-control" placeholder="Tapez le code, nom ou prénom...">
                                    <input type="hidden" name="patient_id" id="patientIdTransfert">
                                    <div id="selectedPatientTransfert" class="alert alert-info mt-2 d-none"></div>
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

                                @if(isset($visiteEnCours) && $visiteEnCours)
                                <div id="infosTransfert" class="alert alert-secondary mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Visite en cours depuis le {{ $visiteEnCours->date_debut->format('d/m/Y à H:i') }}</strong><br>
                                    @php
                                        $dernierMouvement = $visiteEnCours->mouvements()->latest('heure_arrivee')->first();
                                        $litActuel = $visiteEnCours->getLitActuel();
                                    @endphp
                                    Service actuel: {{ $dernierMouvement && $dernierMouvement->service ? $dernierMouvement->service->nom_service : 'Non déterminé' }}<br>
                                    Lit actuel: {{ $litActuel ? 'N°' . $litActuel->numero : 'Aucun' }}
                                </div>
                                @elseif(isset($patient) && $patient)
                                <div id="infosTransfert" class="alert alert-warning mb-3">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <strong>Aucune visite en cours</strong><br>
                                    Ce patient n'a pas de visite active. Pour effectuer un transfert, une visite doit être en cours.
                                </div>
                                @else
                                <div id="infosTransfert" class="alert alert-secondary mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <span id="infoMessage">Sélectionnez d'abord un patient pour voir sa situation actuelle.</span>
                                </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label">Nouveau service <span class="text-danger">*</span></label>
                                    <select name="nouveau_service_id" id="serviceTransfert" class="form-select" required {{ (!isset($visiteEnCours) || !$visiteEnCours) ? 'disabled' : '' }}>
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

                                <button type="submit" class="btn btn-warning" {{ (!isset($visiteEnCours) || !$visiteEnCours) ? 'disabled' : '' }}>
                                    <i class="bi bi-arrow-left-right me-1"></i> Effectuer le transfert
                                </button>
                            </form>
                        </div>

                        {{-- Onglet 4 : Valider la sortie --}}
                        <div class="tab-pane fade {{ $activeTab == 'sortie' ? 'show active' : '' }}" id="sortie" role="tabpanel">
                            <form method="POST" action="{{ route('mouvements.sortie') }}" id="formSortie">
                                @csrf

                                @if(!isset($patient) || !$patient)
                                <div class="mb-3">
                                    <label class="form-label">Patient <span class="text-danger">*</span></label>
                                    <input type="text" id="searchPatientSortie" class="form-control" placeholder="Tapez le code, nom ou prénom...">
                                    <input type="hidden" name="patient_id" id="patientIdSortie">
                                    <div id="selectedPatientSortie" class="alert alert-info mt-2 d-none"></div>
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

                                @if(isset($visiteEnCours) && $visiteEnCours)
                                <div id="infosSortie" class="alert alert-secondary mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Visite N°{{ $visiteEnCours->numero_visite }} en cours</strong><br>
                                    Début: {{ $visiteEnCours->date_debut->format('d/m/Y à H:i') }}<br>
                                    Durée: {{ $visiteEnCours->getDuree() }}<br>
                                    @php
                                        $litActuel = $visiteEnCours->getLitActuel();
                                    @endphp
                                    Lit actuel: {{ $litActuel ? 'N°' . $litActuel->numero : 'Aucun' }}
                                </div>
                                @elseif(isset($patient) && $patient)
                                <div id="infosSortie" class="alert alert-warning mb-3">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <strong>Aucune visite en cours</strong><br>
                                    Ce patient n'a pas de visite active à clôturer.
                                </div>
                                @else
                                <div id="infosSortie" class="alert alert-secondary mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <span id="infoMessageSortie">Sélectionnez d'abord un patient pour voir sa situation actuelle.</span>
                                </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label">Note (optionnelle)</label>
                                    <textarea name="note" class="form-control" rows="3" placeholder="Informations de sortie..."></textarea>
                                    @error('note')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <button type="submit" class="btn btn-danger" id="btnSortie" {{ (!isset($visiteEnCours) || !$visiteEnCours) ? 'disabled' : '' }}>
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
// Liste des patients pour la recherche (passée depuis PHP via une variable)
var patientsData = {!! json_encode(\App\Models\Patient::select('id', 'code_unique', 'nom', 'prenom')->get()->toArray()) !!};

// Fonction de recherche patient
function setupPatientSearch(inputId, hiddenId, selectedDivId, callback) {
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
            if (callback) callback(selected.id);
        } else if (this.value.trim() !== '') {
            document.getElementById(hiddenId).value = '';
            document.getElementById(selectedDivId).classList.add('d-none');
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

// Initialisation des recherches patient
setupPatientSearch('searchPatientPassage', 'patientIdPassage', 'selectedPatientPassage');
setupPatientSearch('searchPatientAdmission', 'patientIdAdmission', 'selectedPatientAdmission');
setupPatientSearch('searchPatientTransfert', 'patientIdTransfert', 'selectedPatientTransfert', function(patientId) {
    if (patientId) {
        fetch('/patients/' + patientId)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                var message = document.getElementById('infoMessage');
                if (data.visite_en_cours) {
                    message.innerHTML = '<strong>Visite en cours depuis le ' + new Date(data.visite_en_cours.date_debut).toLocaleString() + '</strong><br>' +
                        'Service actuel: ' + (data.service_actuel || 'Non déterminé') + '<br>' +
                        'Lit actuel: ' + (data.lit_actuel ? 'N°' + data.lit_actuel.numero : 'Aucun');
                    document.getElementById('visiteIdTransfert').value = data.visite_en_cours.id;
                    document.getElementById('serviceTransfert').disabled = false;
                } else {
                    message.innerHTML = '<strong>Aucune visite en cours</strong><br>Ce patient n\'a pas de visite active.';
                    document.getElementById('visiteIdTransfert').value = '';
                    document.getElementById('serviceTransfert').disabled = true;
                    document.getElementById('serviceTransfert').value = '';
                    document.getElementById('litTransfert').innerHTML = '<option value="">-- Choisissez d\'abord un service --</option>';
                    document.getElementById('litTransfert').disabled = true;
                }
            })
            .catch(function(e) { console.error(e); });
    }
});
setupPatientSearch('searchPatientSortie', 'patientIdSortie', 'selectedPatientSortie', function(patientId) {
    if (patientId) {
        fetch('/patients/' + patientId)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                var message = document.getElementById('infoMessageSortie');
                var btn = document.getElementById('btnSortie');
                if (data.visite_en_cours) {
                    message.innerHTML = '<strong>Visite N°' + data.visite_en_cours.numero_visite + ' en cours</strong><br>' +
                        'Début: ' + new Date(data.visite_en_cours.date_debut).toLocaleString() + '<br>' +
                        'Lit actuel: ' + (data.lit_actuel ? 'N°' + data.lit_actuel.numero : 'Aucun');
                    document.getElementById('visiteIdSortie').value = data.visite_en_cours.id;
                    btn.disabled = false;
                } else {
                    message.innerHTML = '<strong>Aucune visite en cours</strong><br>Ce patient n\'a pas de visite active à clôturer.';
                    document.getElementById('visiteIdSortie').value = '';
                    btn.disabled = true;
                }
            })
            .catch(function(e) { console.error(e); });
    }
});

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
</style>
@endif

@endsection
