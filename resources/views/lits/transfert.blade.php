@extends('layouts.app')
@section('title', 'Transférer un lit')
@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card animate__animated animate__fadeIn">
            <div class="card-header">
                <i class="bi bi-arrow-left-right me-2"></i>Transférer un lit
            </div>
            <div class="card-body">
                @if(session('error'))
                <div class="alert alert-danger animate__animated animate__shakeX">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('lits.transfert') }}" id="transfertForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Lit à transférer <span class="text-danger">*</span></label>
                        <select name="lit_id" id="litSelect" class="form-select @error('lit_id') is-invalid @enderror" required>
                            <option value="">-- Sélectionner un lit --</option>
                            @foreach($litsLibres as $lit)
                            <option value="{{ $lit->id }}" data-numero="{{ $lit->numero }}">
                                Lit N°{{ $lit->numero }} - {{ $lit->salle->service->nom_service }} / {{ $lit->salle->nom }}
                            </option>
                            @endforeach
                        </select>
                        @error('lit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Seuls les lits libres peuvent être transférés.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nouvelle salle <span class="text-danger">*</span></label>
                        <select name="salle_id" id="salleSelect" class="form-select @error('salle_id') is-invalid @enderror" required>
                            <option value="">-- Sélectionner une salle --</option>
                            @foreach($salles as $salle)
                            <option value="{{ $salle->id }}" data-capacite="{{ $salle->capacite }}" data-lits="{{ $salle->lits->count() }}">
                                {{ $salle->service->nom_service }} - {{ $salle->nom }} ({{ $salle->lits->count() }}/{{ $salle->capacite }} lits)
                            </option>
                            @endforeach
                        </select>
                        @error('salle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text" id="transfertInfo"></div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Le lit conservera son numéro. Assurez-vous que ce numéro n'existe pas déjà dans la nouvelle salle.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning" id="btnTransfert">
                            <i class="bi bi-arrow-left-right me-1"></i>Transférer
                        </button>
                        <a href="{{ route('lits.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('salleSelect').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const capacite = option.dataset.capacite;
    const litsActuels = option.dataset.lits;
    const infoDiv = document.getElementById('transfertInfo');

    if (capacite && litsActuels) {
        if (parseInt(litsActuels) >= parseInt(capacite)) {
            infoDiv.innerHTML = '<span class="text-danger">⚠️ Cette salle a atteint sa capacité maximale (' + capacite + ' lits). Impossible d\'y transférer un lit.</span>';
            document.getElementById('btnTransfert').disabled = true;
        } else {
            // Vérifier aussi si le numéro du lit existe déjà
            const litSelect = document.getElementById('litSelect');
            const litOption = litSelect.options[litSelect.selectedIndex];
            const litNumero = litOption ? litOption.dataset.numero : null;

            infoDiv.innerHTML = '<span class="text-success">✓ Cette salle peut encore accueillir ' + (capacite - litsActuels) + ' lit(s).</span>';
            document.getElementById('btnTransfert').disabled = false;
        }
    }
});

document.getElementById('litSelect').addEventListener('change', function() {
    // Re-déclencher la vérification de la salle
    document.getElementById('salleSelect').dispatchEvent(new Event('change'));
});
</script>

@endsection
