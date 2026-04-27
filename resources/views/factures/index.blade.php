@extends('layouts.app')
@section('title','Factures')
@section('content')

{{-- Statistiques --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--blue-l);color:var(--blue)"><i class="bi bi-receipt-cutoff"></i></div>
            <div><div class="stat-num" style="color:var(--blue)">{{ $factures->total() }}</div><div class="stat-lbl">Factures</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="animation-delay:.05s">
            <div class="stat-icon" style="background:#F0FDF4;color:#16A34A"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="stat-num" style="color:#16A34A;font-size:1.3rem">{{ number_format($totalMontant,0,',',' ') }}</div>
                <div class="stat-lbl">FCFA total brut</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="animation-delay:.1s">
            <div class="stat-icon" style="background:#EFF6FF;color:#2563EB"><i class="bi bi-shield-fill-check"></i></div>
            <div>
                <div class="stat-num" style="color:#2563EB;font-size:1.3rem">{{ number_format($totalPEC,0,',',' ') }}</div>
                <div class="stat-lbl">FCFA prise en charge</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="animation-delay:.15s">
            <div class="stat-icon" style="background:var(--amber-l,#FFFBEB);color:var(--amber,#D97706)"><i class="bi bi-person-fill"></i></div>
            <div>
                <div class="stat-num" style="color:var(--amber,#D97706);font-size:1.3rem">{{ number_format($totalPatient,0,',',' ') }}</div>
                <div class="stat-lbl">FCFA charge patients</div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <span style="color:var(--muted);font-size:.88rem">{{ $factures->total() }} facture(s)</span>
    <a href="{{ route('factures.create') }}" class="btn btn-success"><i class="bi bi-receipt me-1"></i>Nouvelle facture</a>
</div>

<div class="card mb-3">
    <div class="card-body py-3 px-4">
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <div class="position-relative flex-grow-1" style="max-width:420px">
                <i class="bi bi-search position-absolute" style="left:11px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.85rem"></i>
                <input type="text" id="factureSearch" class="form-control"
                       style="padding-left:32px" value="{{ request('search') }}"
                       placeholder="N° reçu, nom patient, code…" autocomplete="off">
            </div>
            <form method="GET" id="factureForm">
                <input type="hidden" name="search" id="factureHidden" value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Rechercher</button>
            </form>
            @if(request('search'))
            <a href="{{ route('factures.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x me-1"></i>Effacer</a>
            @endif
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($factures->isEmpty())
        <div class="text-center py-5" style="color:var(--muted)">
            <i class="bi bi-receipt fs-1 d-block mb-2 opacity-25"></i>Aucune facture enregistrée.
        </div>
        @else
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>N° reçu</th>
                    <th>Patient</th>
                    <th>Service</th>
                    <th class="text-end">Solde patient</th>
                    <th class="text-end">Prise en charge</th>
                    <th class="text-end">Total (FCFA)</th>
                    <th>Date</th>
                    <th>Agent</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="factureBody">
            @foreach($factures as $f)
            <tr>
                <td><span class="code-badge">{{ $f->numero_facture }}</span></td>
                <td>
                    <a href="{{ route('patients.show',$f->patient_id) }}" class="fw-semibold text-decoration-none" style="color:var(--text)">
                        {{ $f->patient->prenom }} {{ $f->patient->nom }}
                    </a>
                    <div style="font-size:.75rem;color:var(--muted)">{{ $f->patient->code_unique }}</div>
                </td>
                <td style="font-size:.85rem">{{ $f->service->nom_service ?? '—' }}</td>
                <td class="text-end fw-bold" style="color:var(--green)">
                    {{ number_format($f->montant_patient,0,',',' ') }}
                </td>
                <td class="text-end">
                    @if($f->has_p_e_c)
                    <div style="color:#2563EB;font-weight:600;font-size:.85rem">
                        {{ number_format($f->pec_montant,0,',',' ') }}
                    </div>
                    <div style="font-size:.72rem;color:var(--muted);max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $f->pec_organisme }}">
                        {{ $f->pec_organisme }}
                    </div>
                    @else
                    <span style="color:var(--muted);font-size:.82rem">—</span>
                    @endif
                </td>

                <td class="text-end fw-semibold" style="color:var(--text)">
                    {{ number_format($f->montant,0,',',' ') }}
                </td>
                <td style="font-size:.84rem">{{ $f->date_facture->format('d/m/Y') }}</td>
                <td style="font-size:.78rem;color:var(--muted)">{{ $f->user->prenom??'' }} {{ $f->user->nom??'' }}</td>
                <td class="text-end">
                    <a href="{{ route('patients.show',$f->patient_id) }}" class="btn btn-sm btn-outline-primary me-1" title="Voir patient"><i class="bi bi-eye"></i></a>
                    @if(Auth::user()->role==='admin')
                    <a href="{{ route('factures.edit',$f) }}" class="btn btn-sm btn-outline-secondary me-1" title="Modifier"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('factures.destroy',$f) }}" class="d-inline"
                          onsubmit="return confirm('Supprimer cette facture ?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" title="Supprimer"><i class="bi bi-trash"></i></button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>
    @if($factures->hasPages())
    <div class="card-footer"><div class="d-flex justify-content-center">{{ $factures->links() }}</div></div>
    @endif
</div>
@endsection

@section('scripts')
<script>
const fs=document.getElementById('factureSearch');
const fh=document.getElementById('factureHidden');
const fr=document.querySelectorAll('#factureBody tr');
fs.addEventListener('input',function(){
    const v=this.value.toLowerCase().trim();fh.value=this.value;
    fr.forEach(r=>{r.style.display=v===''||r.innerText.toLowerCase().includes(v)?'':'none'});
});
document.getElementById('factureForm').addEventListener('submit',()=>{fh.value=fs.value});
</script>
@endsection
