@extends('layouts.app')
@section('title','Modifier la facture')
@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-pencil" style="color:var(--amber)"></i> Modifier la facture
    </div>
    <div class="card-body p-4">
        @if($errors->any())
        <div class="alert alert-danger mb-3"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <div class="alert alert-secondary mb-4" style="font-size:.83rem">
            Patient : <strong>{{ $facture->patient->prenom }} {{ $facture->patient->nom }}</strong>
            — <span class="code-badge">{{ $facture->patient->code_unique }}</span>
        </div>
        <form method="POST" action="{{ route('factures.update',$facture) }}" id="formEditF" novalidate>
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">N° de reçu *</label>
                    <input type="text" name="numero_facture" id="numFE" value="{{ old('numero_facture',$facture->numero_facture) }}"
                           class="form-control @error('numero_facture') is-invalid @enderror" required>
                    <div class="invalid-feedback" id="numFE-err">{{ $errors->first('numero_facture') ?: 'Format invalide.' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date *</label>
                    <input type="date" name="date_facture" value="{{ old('date_facture',$facture->date_facture->format('Y-m-d')) }}"
                           class="form-control" max="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Service *</label>
                    <select name="service_id" class="form-select" required>
                        @foreach($services as $s)
                        <option value="{{ $s->id }}" {{ $facture->service_id==$s->id?'selected':'' }}>{{ $s->nom_service }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Montant total (FCFA) *</label>
                    <input type="number" name="montant" id="montantFE" value="{{ old('montant',$facture->montant) }}"
                           class="form-control @error('montant') is-invalid @enderror" min="1" step="1" required>
                    <div class="invalid-feedback">{{ $errors->first('montant') ?: 'Montant invalide.' }}</div>
                </div>
            </div>

            {{-- Prise en charge --}}
            <div class="mt-4 p-3" style="background:#F8FAFD;border-radius:10px;border:1.5px dashed #DDE3EC">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="pecToggleE"
                               {{ old('pec_organisme',$facture->pec_organisme) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="pecToggleE" style="font-size:.88rem">
                            <i class="bi bi-shield-fill-check me-1" style="color:var(--green)"></i>
                            Prise en charge par un organisme ?
                        </label>
                    </div>
                </div>
                <div id="pecFieldsE" style="{{ old('pec_organisme',$facture->pec_organisme) ? '' : 'display:none' }}">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label">Nom de l'organisme</label>
                            <input type="text" name="pec_organisme" value="{{ old('pec_organisme',$facture->pec_organisme) }}"
                                   class="form-control" placeholder="Ex: CNSS, RAMU, Min Ens Sec…">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Montant PEC (FCFA)</label>
                            <input type="number" name="pec_montant" id="pecMntE" value="{{ old('pec_montant',$facture->pec_montant) }}"
                                   class="form-control @error('pec_montant') is-invalid @enderror"
                                   min="0" step="1" oninput="calcResteE()">
                            <div class="invalid-feedback">{{ $errors->first('pec_montant') }}</div>
                        </div>
                        <div class="col-12">
                            <div id="resteBoxE" style="display:none;background:var(--blue-l);border-radius:8px;padding:10px 14px">
                                <div class="d-flex justify-content-between" style="font-size:.85rem">
                                    <span style="color:var(--muted)">Montant total :</span>
                                    <span class="fw-semibold" id="dispTotalE">—</span>
                                </div>
                                <div class="d-flex justify-content-between" style="font-size:.85rem;margin-top:4px">
                                    <span style="color:var(--green)">Prise en charge :</span>
                                    <span style="color:var(--green);font-weight:600" id="dispPECE">—</span>
                                </div>
                                <div class="d-flex justify-content-between" style="font-size:.9rem;margin-top:6px;padding-top:6px;border-top:1px solid rgba(0,90,156,.15)">
                                    <span class="fw-semibold" style="color:var(--blue-d)">Solde patient :</span>
                                    <span class="fw-bold" style="color:var(--blue-d);font-size:1rem" id="dispResteE">—</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 pt-3">
                <button type="submit" class="btn btn-warning"><i class="bi bi-check-circle me-1"></i>Enregistrer</button>
                <a href="{{ route('patients.show',$facture->patient_id) }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('pecToggleE').addEventListener('change',function(){
    document.getElementById('pecFieldsE').style.display=this.checked?'block':'none';
    if(!this.checked){document.getElementById('pecMntE').value='';document.getElementById('resteBoxE').style.display='none';}
});
function calcResteE(){
    const t=parseFloat(document.getElementById('montantFE').value)||0;
    const p=parseFloat(document.getElementById('pecMntE').value)||0;
    const r=t-p,box=document.getElementById('resteBoxE');
    if(t>0&&p>=0){
        box.style.display='block';
        document.getElementById('dispTotalE').textContent=t.toLocaleString('fr-FR')+' FCFA';
        document.getElementById('dispPECE').textContent=p.toLocaleString('fr-FR')+' FCFA';
        document.getElementById('dispResteE').textContent=r.toLocaleString('fr-FR')+' FCFA';
    } else box.style.display='none';
}
document.getElementById('montantFE').addEventListener('input',calcResteE);
// Init
if(document.getElementById('pecMntE').value)calcResteE();

// Validation
const nR=/^[A-Za-z0-9\-\/]+$/;
document.getElementById('formEditF').addEventListener('submit',function(e){
    let ok=true;
    const n=document.getElementById('numFE');
    if(!n.value.trim()||!nR.test(n.value)){n.classList.add('is-invalid');document.getElementById('numFE-err').textContent='Format invalide.';ok=false;}
    const m=document.getElementById('montantFE');
    if(!m.value||parseFloat(m.value)<=0){m.classList.add('is-invalid');ok=false;}
    if(!ok)e.preventDefault();
});
</script>
@endsection
