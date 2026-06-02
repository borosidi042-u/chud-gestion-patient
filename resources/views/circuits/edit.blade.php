@extends('layouts.app')
@section('title', 'Modifier un mouvement')
@section('content')

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card animate__animated animate__fadeIn">
            <div class="card-header">
                <i class="bi bi-pencil me-2" style="color:var(--amber)"></i> Modifier le mouvement
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
                    @csrf
                    @method('PUT')

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

                    <div class="alert alert-info py-2 mb-4" style="font-size:.82rem">
                        <i class="bi bi-info-circle me-1"></i>
                        Type de mouvement :
                        @if($circuit->is_entry)
                            <strong class="text-success">Admission (entrée)</strong>
                        @elseif($circuit->is_exit)
                            <strong class="text-danger">Sortie</strong>
                        @else
                            <strong class="text-warning">Passage / Transfert</strong>
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
