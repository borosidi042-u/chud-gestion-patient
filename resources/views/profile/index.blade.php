@extends('layouts.app')
@section('title','Mon profil')
@section('content')

<div class="row justify-content-center">
<div class="col-lg-8">

{{-- En-tête profil --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <div style="width:72px;height:72px;border-radius:50%;flex-shrink:0;
                background:var(--blue-l);color:var(--blue);
                display:flex;align-items:center;justify-content:center;
                font-weight:700;font-size:1.5rem;border:3px solid var(--blue)">
                {{ strtoupper(substr($user->prenom,0,1).substr($user->nom,0,1)) }}
            </div>
            <div>
                <h4 class="fw-bold mb-1" style="font-size:1.15rem">{{ $user->prenom }} {{ $user->nom }}</h4>
                <div style="font-size:.84rem;color:var(--muted)">
                    <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                </div>
                <div style="margin-top:5px">
                    <span style="border-radius:20px;padding:3px 12px;font-size:.72rem;font-weight:600;
                        background:{{ $user->role==='admin'?'var(--red-l)':'var(--blue-l)' }};
                        color:{{ $user->role==='admin'?'var(--red)':'var(--blue)' }}">
                        <i class="bi {{ $user->role==='admin'?'bi-shield-fill':'bi-person-fill' }} me-1"></i>
                        {{ $user->role==='admin'?'Administrateur':"Agent d'accueil" }}
                    </span>
                </div>
            </div>
            <div class="ms-auto text-end" style="font-size:.78rem;color:var(--muted)">
                <div>Compte créé le</div>
                <div class="fw-semibold">{{ $user->created_at->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Onglets --}}
<ul class="nav nav-pills mb-3 gap-2" id="profileTabs">
    <li class="nav-item">
        <button class="nav-link {{ session('tab')==='password'?'':'active' }}"
                id="tab-info" onclick="showTab('info')" type="button"
                style="border-radius:9px;font-size:.875rem;font-weight:500">
            <i class="bi bi-person-fill me-1"></i>Informations
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link {{ session('tab')==='password'?'active':'' }}"
                id="tab-pw" onclick="showTab('password')" type="button"
                style="border-radius:9px;font-size:.875rem;font-weight:500">
            <i class="bi bi-lock-fill me-1"></i>Mot de passe
        </button>
    </li>
</ul>

{{-- Messages flash --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ONGLET 1 : Informations personnelles --}}
<div id="pane-info" class="{{ session('tab')==='password'?'d-none':'' }}">
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-person-fill" style="color:var(--blue)"></i>
        Modifier mes informations
    </div>
    <div class="card-body p-4">
        @if($errors->hasBag('default') && !session('tab'))
        <div class="alert alert-danger mb-3">
            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif
        <form method="POST" action="{{ route('profile.update-info') }}" id="infoForm" novalidate>
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="nom" id="iNom" value="{{ old('nom',$user->nom) }}"
                           class="form-control @error('nom') is-invalid @enderror"
                           required autocomplete="family-name">
                    <div class="invalid-feedback" id="iNom-err">{{ $errors->first('nom') ?: 'Lettres uniquement.' }}</div>
                    <div class="field-hint">Lettres, espaces et tirets uniquement.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Prénom <span class="text-danger">*</span></label>
                    <input type="text" name="prenom" id="iPrenom" value="{{ old('prenom',$user->prenom) }}"
                           class="form-control @error('prenom') is-invalid @enderror"
                           required autocomplete="given-name">
                    <div class="invalid-feedback" id="iPrenom-err">{{ $errors->first('prenom') ?: 'Lettres uniquement.' }}</div>
                </div>
                <div class="col-12">
                    <label class="form-label">Adresse email</label>
                    <input type="email" value="{{ $user->email }}" class="form-control"
                           disabled style="background:#F5F8FC;color:var(--muted)">
                    <div class="field-hint">L'adresse email ne peut pas être modifiée.</div>
                </div>
                <div class="col-12 pt-1 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Enregistrer les modifications
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </div>
        </form>
    </div>
</div>
</div>

{{-- ONGLET 2 : Modifier le mot de passe --}}
<div id="pane-password" class="{{ session('tab')==='password'?'':'d-none' }}">
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-lock-fill" style="color:var(--blue)"></i>
        Modifier mon mot de passe
    </div>
    <div class="card-body p-4">
        @if($errors->has('current_password') || (session('tab')==='password' && $errors->any()))
        <div class="alert alert-danger mb-3">
            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif
        <form method="POST" action="{{ route('profile.update-password') }}" id="pwForm" novalidate>
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Mot de passe actuel <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input type="password" name="current_password" id="curPw"
                               class="form-control pe-5 @error('current_password') is-invalid @enderror"
                               placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="btn btn-sm position-absolute end-0 top-50 translate-middle-y me-1"
                                style="background:none;border:none;color:var(--muted)"
                                onclick="togglePw('curPw','eyeCur')">
                            <i id="eyeCur" class="bi bi-eye-slash"></i>
                        </button>
                        @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Nouveau mot de passe <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input type="password" name="password" id="newPw"
                               class="form-control pe-5 @error('password') is-invalid @enderror"
                               placeholder="Min. 8 caractères" required autocomplete="new-password">
                        <button type="button" class="btn btn-sm position-absolute end-0 top-50 translate-middle-y me-1"
                                style="background:none;border:none;color:var(--muted)"
                                onclick="togglePw('newPw','eyeNew')">
                            <i id="eyeNew" class="bi bi-eye-slash"></i>
                        </button>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div style="height:4px;border-radius:2px;background:#EEF2F7;margin-top:6px">
                        <div id="pwStrBar" style="height:100%;border-radius:2px;transition:all .3s;width:0"></div>
                    </div>
                    <div id="pwStrLbl" style="font-size:.72rem;margin-top:3px"></div>
                </div>
                <div class="col-12">
                    <label class="form-label">Confirmer le nouveau mot de passe <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input type="password" name="password_confirmation" id="confPw"
                               class="form-control pe-5" placeholder="Répétez" required autocomplete="new-password">
                        <button type="button" class="btn btn-sm position-absolute end-0 top-50 translate-middle-y me-1"
                                style="background:none;border:none;color:var(--muted)"
                                onclick="togglePw('confPw','eyeConf')">
                            <i id="eyeConf" class="bi bi-eye-slash"></i>
                        </button>
                        <div class="invalid-feedback" id="confErr" style="display:none">
                            Les mots de passe ne correspondent pas.
                        </div>
                    </div>
                </div>
                <div class="col-12 pt-1 d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="btnPw">
                        <i class="bi bi-shield-check me-1"></i>Modifier le mot de passe
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </div>
        </form>
    </div>
</div>
</div>

</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Onglets ──────────────────────────────────────────────────────────────
function showTab(tab) {
    document.getElementById('pane-info').classList.toggle('d-none', tab !== 'info');
    document.getElementById('pane-password').classList.toggle('d-none', tab !== 'password');
    document.getElementById('tab-info').classList.toggle('active', tab === 'info');
    document.getElementById('tab-pw').classList.toggle('active', tab === 'password');
}

// ── Toggle œil ───────────────────────────────────────────────────────────
function togglePw(inpId, icId) {
    const el=document.getElementById(inpId),ic=document.getElementById(icId);
    el.type=el.type==='password'?'text':'password';
    ic.className=el.type==='password'?'bi bi-eye-slash':'bi bi-eye';
}

// ── Bloquer chiffres nom/prénom ──────────────────────────────────────────
['iNom','iPrenom'].forEach(id=>{
    const el=document.getElementById(id);
    if(el)el.addEventListener('input',function(){this.value=this.value.replace(/[0-9]/g,'');});
});

// ── Validation info ──────────────────────────────────────────────────────
const lettres=/^[\p{L}\s\-']+$/u;
document.getElementById('infoForm').addEventListener('submit',function(e){
    let ok=true;
    const nom=document.getElementById('iNom'),pr=document.getElementById('iPrenom');
    [nom,pr].forEach(el=>el.classList.remove('is-invalid'));
    if(!nom.value.trim()||!lettres.test(nom.value)){nom.classList.add('is-invalid');ok=false;}
    if(!pr.value.trim()||!lettres.test(pr.value)){pr.classList.add('is-invalid');ok=false;}
    if(!ok)e.preventDefault();
});

// ── Force mot de passe ───────────────────────────────────────────────────
const newPw=document.getElementById('newPw');
if(newPw)newPw.addEventListener('input',function(){
    const v=this.value,bar=document.getElementById('pwStrBar'),lbl=document.getElementById('pwStrLbl');
    let s=0;if(v.length>=8)s++;if(v.length>=12)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;
    const lv=[{w:'0%',c:'#EEF2F7',t:''},{w:'20%',c:'var(--red)',t:'Très faible'},{w:'40%',c:'#F59E0B',t:'Faible'},{w:'65%',c:'#D97706',t:'Moyen'},{w:'85%',c:'var(--green)',t:'Fort'},{w:'100%',c:'#065F46',t:'Très fort'}][Math.min(s,5)];
    bar.style.width=lv.w;bar.style.background=lv.c;lbl.textContent=lv.t;lbl.style.color=lv.c;
});

// ── Validation mot de passe ──────────────────────────────────────────────
const pwForm=document.getElementById('pwForm');
if(pwForm)pwForm.addEventListener('submit',function(e){
    let ok=true;
    const pw=document.getElementById('newPw'),conf=document.getElementById('confPw');
    const confErr=document.getElementById('confErr');
    conf.classList.remove('is-invalid');confErr.style.display='none';
    if(!pw.value||pw.value.length<8){pw.classList.add('is-invalid');ok=false;}
    if(pw.value!==conf.value){conf.classList.add('is-invalid');confErr.style.display='block';ok=false;}
    if(!ok)e.preventDefault();
});
</script>
@endsection