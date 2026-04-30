@extends('layouts.app')
@section('title', 'Transférer les données')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-warning bg-opacity-10 d-flex align-items-center gap-2">
                <i class="bi bi-shuffle fs-4" style="color:var(--amber)"></i>
                <span class="fw-semibold">Transférer les données</span>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-warning mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Attention !</strong> Le compte <strong>{{ $oldUser->prenom }} {{ $oldUser->nom }}</strong>
                    a des données (factures, passages) qui l'empêchent d'être supprimé.
                </div>

                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Vous pouvez transférer toutes ses données à un autre utilisateur avant de le supprimer.
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold mb-2">📊 Données à transférer :</h6>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Factures enregistrées
                            <span class="badge bg-primary rounded-pill">{{ $facturesCount }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Passages enregistrés
                            <span class="badge bg-primary rounded-pill">{{ $circuitsCount }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Patients créés
                            <span class="badge bg-primary rounded-pill">{{ $patientsCount }}</span>
                        </li>
                    </ul>
                </div>

                <form method="POST" action="{{ route('admin.users.transfer', $oldUser->id) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-person-badge me-1"></i> Transférer vers :
                        </label>
                        <select name="new_user_id" class="form-select" required>
                            <option value="">-- Choisir un utilisateur --</option>
                            @foreach($users as $user)
                                @if($user->id !== $oldUser->id)
                                <option value="{{ $user->id }}">
                                    {{ $user->prenom }} {{ $user->nom }}
                                    ({{ $user->role === 'admin' ? 'Admin' : 'Agent' }})
                                    {{ $user->id === Auth::id() ? ' - Vous' : '' }}
                                </option>
                                @endif
                            @endforeach
                        </select>
                        <div class="field-hint mt-1">Les données seront définitivement transférées à cet utilisateur.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-shuffle me-1"></i> Transférer et supprimer
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
