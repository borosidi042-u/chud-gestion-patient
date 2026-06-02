@extends('layouts.app')
@section('title', 'Supprimer un lit')
@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card animate__animated animate__fadeIn">
            <div class="card-header bg-danger text-white">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Supprimer un lit
            </div>
            <div class="card-body">
                @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="alert alert-warning mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-4 float-start"></i>
                    <div class="ms-4">
                        <strong>Attention !</strong><br>
                        Vous êtes sur le point de supprimer définitivement le lit <strong>N°{{ $lit->numero }}</strong>.
                        Cette action est irréversible.
                    </div>
                </div>

                @if($lit->statut === 'occupe')
                <div class="alert alert-danger mb-4">
                    <i class="bi bi-x-circle-fill me-2"></i>
                    <strong>Suppression impossible :</strong> Ce lit est actuellement occupé par le patient
                    @if($lit->patient){{ $lit->patient->prenom }} {{ $lit->patient->nom }} (Code: {{ $lit->patient->code_unique }})@endif.
                    <br>Veuillez d'abord valider la sortie du patient avant de supprimer le lit.
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('lits.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-1"></i>Retour
                    </a>
                </div>
                @else
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold">Informations du lit :</h6>
                        <table class="table table-sm">
                            <tr><th width="35%">Numéro</th><td><strong>Lit N°{{ $lit->numero }}</strong></td></tr>
                            <tr><th>Statut</th>
                                <td>
                                    <span class="badge
                                        @if($lit->statut === 'libre') bg-success
                                        @elseif($lit->statut === 'maintenance') bg-warning text-dark
                                        @else bg-secondary
                                        @endif">
                                        {{ ucfirst($lit->statut) }}
                                    </span>
                                </td>
                            </tr>
                            <tr><th>Salle</th><td>{{ $lit->salle->nom ?? '—' }}</td></tr>
                            <tr><th>Service</th><td>{{ $lit->salle->service->nom_service ?? '—' }}</td></tr>
                        </table>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.lits.destroy', $lit) }}" id="formDeleteLit">
                    @csrf
                    @method('DELETE')

                    <div class="alert alert-danger mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Êtes-vous sûr de vouloir supprimer ce lit ? Cette action est définitive.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Supprimer définitivement le lit N°{{ $lit->numero }} ?')">
                            <i class="bi bi-trash me-1"></i>Confirmer la suppression
                        </button>
                        <a href="{{ route('lits.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Annuler
                        </a>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
