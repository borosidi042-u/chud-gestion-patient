@extends('layouts.app')
@section('title','Modifier un passage')
@section('content')

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card animate__animated animate__fadeIn">
            <div class="card-header">
                <i class="bi bi-pencil me-2" style="color:var(--amber)"></i> Modifier le passage
            </div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
                @endif

                <div class="alert alert-secondary mb-4" style="font-size:.85rem">
                    <i class="bi bi-person me-1"></i>
                    Patient : <strong>{{ $patient->prenom }} {{ $patient->nom }}</strong>
                    — <span class="code-badge">{{ $patient->code_unique }}</span>
                </div>

                <form method="POST" action="{{ route('circuits.update', $circuit) }}">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label class="form-label">Service <span class="text-danger">*</span></label>
                        <select name="service_id" class="form-select" required>
                            <option value="">— Sélectionner un service —</option>
                            @foreach($services as $s)
                            <option value="{{ $s->id }}" {{ $circuit->service_id == $s->id ? 'selected' : '' }}>
                                {{ $s->nom_service }}@if($s->description) — {{ $s->description }}@endif
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <div class="alert alert-info py-2 mb-3" style="font-size:.82rem">
                            <i class="bi bi-info-circle me-1"></i>
                            @if($circuit->is_entry)
                                Ce circuit est marqué comme <strong>Début de visite</strong> (automatique).
                            @else
                                Ce circuit est un passage intermédiaire.
                            @endif
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="is_exit" id="is_exit" value="1"
                                   class="form-check-input" {{ $circuit->is_exit ? 'checked' : '' }}
                                   {{ isset($isLastCircuit) && $isLastCircuit ? '' : 'disabled' }}>
                            <label class="form-check-label" for="is_exit">
                                <i class="bi bi-stop-circle-fill text-danger me-1"></i>
                                Fin de la visite (Sortie de l'hôpital)
                            </label>
                        </div>
                        <div class="field-hint mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Seul le dernier circuit d'une visite peut être marqué comme "Fin de visite".<br>
                            Le début de la visite est automatique (premier circuit du patient).
                        </div>
                        @error('is_exit')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                        @if(isset($isLastCircuit) && !$isLastCircuit && isset($nextCircuit) && $nextCircuit && $nextCircuit->is_entry)
                        <div class="alert alert-warning mt-2 py-2 small">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Attention : Un circuit existe après celui-ci qui marque le début d'une nouvelle visite.
                            Si vous désactivez "Fin de visite", les circuits suivants rejoindront cette visite.
                        </div>
                        @endif
                    </div>

                    <div class="alert alert-secondary py-2 mb-4" style="font-size:.82rem">
                        <i class="bi bi-clock me-1"></i>
                        Date d'enregistrement : <strong>{{ $circuit->created_at->format('d/m/Y à H:i') }}</strong><br>
                        <i class="bi bi-person me-1"></i>
                        Enregistré par : <strong>{{ $circuit->user->prenom ?? '' }} {{ $circuit->user->nom ?? '' }}</strong>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle me-1"></i> Enregistrer
                        </button>
                        <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
