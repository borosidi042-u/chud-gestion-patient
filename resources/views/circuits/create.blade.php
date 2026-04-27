@extends('layouts.app')
@section('title','Enregistrer un passage')
@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-diagram-3-fill" style="color:var(--blue)"></i> Enregistrement d'un passage dans un service
    </div>
    <div class="card-body p-4">
        @if($errors->any())
        <div class="alert alert-danger mb-3"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ route('circuits.store') }}" id="formCircuit" novalidate>
            @csrf
            @if($patient)
                <div class="alert alert-info mb-4" style="font-size:.85rem">
                    <i class="bi bi-person-check me-1"></i>
                    Patient : <strong>{{ $patient->prenom }} {{ $patient->nom }}</strong>
                    — <span class="code-badge">{{ $patient->code_unique }}</span>
                </div>
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
            @else
                <div class="mb-4">
                    <label class="form-label">Patient <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input type="text" id="pSearch" class="form-control"
                               placeholder="Tapez nom, prénom, code, NPI ou téléphone…" autocomplete="off">
                        <div id="pDrop" class="patient-dropdown" style="display:none"></div>
                    </div>
                    <input type="hidden" name="patient_id" id="pId" value="{{ old('patient_id') }}">
                    <div id="pSel" class="alert alert-info py-2 mt-2" style="display:none;font-size:.83rem"></div>
                    @error('patient_id')<div style="color:var(--red);font-size:.77rem;margin-top:4px">{{ $message }}</div>@enderror
                </div>
            @endif

            <div class="mb-4">
                <label class="form-label">Service <span class="text-danger">*</span></label>
                <select name="service_id" id="svcC" class="form-select @error('service_id') is-invalid @enderror" required>
                    <option value="">— Sélectionner un service —</option>
                    @foreach($services as $s)
                    <option value="{{ $s->id }}" {{ old('service_id')==$s->id?'selected':'' }}>
                        {{ $s->nom_service }}@if($s->description) — {{ $s->description }}@endif
                    </option>
                    @endforeach
                </select>
                <div class="invalid-feedback">{{ $errors->first('service_id') ?: 'Choisir un service.' }}</div>
            </div>

            <div class="alert alert-secondary py-2 mb-4" style="font-size:.82rem">
                <i class="bi bi-clock me-1"></i> Horodatage automatique : <strong>{{ now()->format('d/m/Y H:i') }}</strong>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Enregistrer le passage</button>
                <a href="{{ $patient ? route('patients.show',$patient) : route('patients.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection

@section('scripts')
@php
    $patientsList = \App\Models\Patient::select('id', 'nom', 'prenom', 'code_unique', 'npi', 'telephone')->get();
@endphp
<script>
@unless($patient)
const aP = @json($patientsList);
const ps = document.getElementById('pSearch');
const pd = document.getElementById('pDrop');
const pi = document.getElementById('pId');
const psel = document.getElementById('pSel');

if (ps) {
    ps.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        pd.innerHTML = '';
        if (q.length < 1) {
            pd.style.display = 'none';
            return;
        }
        const r = aP.filter(p =>
            (p.nom || '').toLowerCase().includes(q) ||
            (p.prenom || '').toLowerCase().includes(q) ||
            (p.code_unique || '').includes(q) ||
            (p.npi || '').includes(q) ||
            (p.telephone || '').includes(q)
        ).slice(0, 8);

        if (!r.length) {
            pd.innerHTML = '<div class="patient-dropdown-item" style="color:var(--muted)">Aucun résultat</div>';
        } else {
            r.forEach(p => {
                const d = document.createElement('div');
                d.className = 'patient-dropdown-item';
                d.innerHTML = `<strong>${p.prenom} ${p.nom}</strong> <span style="color:var(--muted);font-size:.78rem">— ${p.code_unique}</span>`;
                d.onclick = () => {
                    pi.value = p.id;
                    ps.value = p.prenom + ' ' + p.nom;
                    pd.style.display = 'none';
                    psel.style.display = 'block';
                    psel.innerHTML = '<i class="bi bi-person-check me-1"></i>Patient : <strong>' + p.prenom + ' ' + p.nom + '</strong> — <span class="code-badge">' + p.code_unique + '</span>';
                };
                pd.appendChild(d);
            });
        }
        pd.style.display = 'block';
    });

    document.addEventListener('click', function(e) {
        if (ps && pd && !ps.contains(e.target) && !pd.contains(e.target)) {
            pd.style.display = 'none';
        }
    });
}
@endunless

document.getElementById('formCircuit').addEventListener('submit', function(e) {
    let ok = true;
    @unless($patient)
    if (!document.getElementById('pId').value) {
        alert('Veuillez sélectionner un patient.');
        ok = false;
    }
    @endunless
    const s = document.getElementById('svcC');
    if (!s.value) {
        s.classList.add('is-invalid');
        ok = false;
    } else {
        s.classList.remove('is-invalid');
    }
    if (!ok) e.preventDefault();
});
</script>
@endsection
