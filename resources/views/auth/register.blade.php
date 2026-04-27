<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHUD B/A — Créer un compte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--blue:#005A9C;--blue-d:#003F6E;--blue-l:#E8F3FC;--green:#00875A;--green-l:#E3F5EF;
              --red:#C8001A;--red-l:#FDECEA;--text:#1A2942;--muted:#6B7A8D;}
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Plus Jakarta Sans',sans-serif;min-height:100vh;
            background:linear-gradient(135deg,var(--blue-d) 0%,var(--blue) 50%,#0077CC 100%);
            display:flex;align-items:center;justify-content:center;padding:20px;
            position:relative;overflow-x:hidden}
        body::before{content:'';position:absolute;width:500px;height:500px;border-radius:50%;
            background:rgba(255,255,255,.04);top:-150px;right:-100px;animation:float 8s ease-in-out infinite}
        body::after{content:'';position:absolute;width:350px;height:350px;border-radius:50%;
            background:rgba(255,255,255,.04);bottom:-100px;left:-80px;animation:float 10s ease-in-out infinite reverse}

        .auth-card{background:#fff;border-radius:20px;width:100%;max-width:480px;
            box-shadow:0 20px 60px rgba(0,0,0,.25);animation:slideUp .5s ease;overflow:hidden;position:relative;z-index:1}
        .auth-header{background:linear-gradient(135deg,var(--blue-d),var(--blue));padding:24px 36px;text-align:center}
        .auth-logo{width:64px;height:64px;background:#fff;border-radius:14px;margin:0 auto 12px;
            display:flex;align-items:center;justify-content:center;overflow:hidden;box-shadow:0 4px 14px rgba(0,0,0,.15)}
        .auth-logo img{width:90%;height:90%;object-fit:contain}
        .auth-hospital{color:#fff;font-weight:700;font-size:1rem}
        .auth-subtitle{color:rgba(255,255,255,.6);font-size:.74rem;margin-top:3px}
        .auth-body{padding:28px 36px 32px}
        .auth-title{font-weight:700;font-size:1.05rem;color:var(--text);margin-bottom:4px}
        .auth-desc{font-size:.81rem;color:var(--muted);margin-bottom:20px}

        .form-label{font-weight:600;font-size:.79rem;color:var(--text);margin-bottom:4px;display:block}
        .input-wrap{position:relative}
        .input-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.9rem;pointer-events:none}
        .form-control{width:100%;border-radius:9px;border:1.5px solid #DDE3EC;
            font-size:.875rem;padding:9px 14px 9px 36px;transition:all .2s;font-family:inherit;color:var(--text)}
        .form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(0,90,156,.12);outline:none}
        .form-control.is-invalid{border-color:var(--red)}
        .form-control.is-valid{border-color:var(--green)}
        .eye-toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);
            border:none;background:none;color:var(--muted);cursor:pointer;font-size:.9rem;padding:0}
        .err-msg{font-size:.75rem;color:var(--red);margin-top:3px;display:none}
        .field-hint{font-size:.73rem;color:var(--muted);margin-top:3px}

        /* Indicateur force mot de passe */
        .pw-strength{height:4px;border-radius:2px;margin-top:6px;transition:all .3s;background:#DDE3EC}
        .pw-strength-bar{height:100%;border-radius:2px;transition:all .3s;width:0}
        .pw-strength-text{font-size:.72rem;margin-top:3px}

        .btn-auth{width:100%;padding:11px;border-radius:9px;background:var(--blue);border:none;
            color:#fff;font-size:.9rem;font-weight:600;cursor:pointer;transition:all .25s;
            font-family:inherit;margin-top:12px}
        .btn-auth:hover{background:var(--blue-d);transform:translateY(-1px);box-shadow:0 6px 18px rgba(0,90,156,.35)}
        .btn-auth:disabled{opacity:.7;cursor:not-allowed;transform:none}
        .auth-footer{text-align:center;margin-top:18px;font-size:.81rem;color:var(--muted)}
        .auth-footer a{color:var(--blue);text-decoration:none;font-weight:600}
        .alert-auth{border:none;border-radius:9px;font-size:.83rem;padding:10px 14px;margin-bottom:16px}
        .alert-danger-auth{background:var(--red-l);color:#7A000F}

        @keyframes slideUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-20px)}}
        @keyframes spin{to{transform:rotate(360deg)}}
        .spinner{width:16px;height:16px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;
            border-radius:50%;animation:spin .6s linear infinite;display:none;
            vertical-align:middle;margin-right:6px}
    </style>
</head>
<body>
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">
            @if(file_exists(public_path('images/image.png')))
                <img src="{{ asset('images/image.png') }}" alt="Logo CHUD-BA">
            @else
                <i class="bi bi-hospital-fill" style="font-size:1.8rem;color:var(--blue)"></i>
            @endif
        </div>
        <div class="auth-hospital">CHUD Borgou-Alibori</div>
        <div class="auth-subtitle">Création de compte agent</div>
    </div>
    <div class="auth-body">
        <div class="auth-title">Créer un compte</div>
        <div class="auth-desc">Remplissez le formulaire pour accéder à l'application.</div>

        @if($errors->any())
        <div class="alert-auth alert-danger-auth">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <ul style="margin:4px 0 0 16px;padding:0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}" id="regForm" novalidate>
            @csrf
            <div class="row g-3">
                <div class="col-6">
                    <label class="form-label">Nom <span style="color:var(--red)">*</span></label>
                    <div class="input-wrap">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" name="nom" id="rNom" value="{{ old('nom') }}"
                               class="form-control" placeholder="KORA" required autocomplete="family-name">
                        <div class="err-msg" id="rNom-err">Lettres uniquement.</div>
                    </div>
                    <div class="field-hint">Pas de chiffres.</div>
                </div>
                <div class="col-6">
                    <label class="form-label">Prénom <span style="color:var(--red)">*</span></label>
                    <div class="input-wrap">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" name="prenom" id="rPrenom" value="{{ old('prenom') }}"
                               class="form-control" placeholder="Moussa" required autocomplete="given-name">
                        <div class="err-msg" id="rPrenom-err">Lettres uniquement.</div>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Adresse email <span style="color:var(--red)">*</span></label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" name="email" id="rEmail" value="{{ old('email') }}"
                               class="form-control" placeholder="agent@chud-ba.bj" required autocomplete="email">
                        <div class="err-msg" id="rEmail-err">Email invalide.</div>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Mot de passe <span style="color:var(--red)">*</span></label>
                    <div class="input-wrap">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" name="password" id="rPw"
                               class="form-control" placeholder="Min. 8 caractères" required autocomplete="new-password">
                        <button type="button" class="eye-toggle" id="eyePw">
                            <i class="bi bi-eye-slash" id="eyePwIc"></i>
                        </button>
                        <div class="err-msg" id="rPw-err">Au moins 8 caractères.</div>
                    </div>
                    <div class="pw-strength"><div class="pw-strength-bar" id="pwBar"></div></div>
                    <div class="pw-strength-text" id="pwText"></div>
                </div>
                <div class="col-12">
                    <label class="form-label">Confirmer le mot de passe <span style="color:var(--red)">*</span></label>
                    <div class="input-wrap">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input type="password" name="password_confirmation" id="rPwC"
                               class="form-control" placeholder="Répétez le mot de passe" required autocomplete="new-password">
                        <button type="button" class="eye-toggle" id="eyePwC">
                            <i class="bi bi-eye-slash" id="eyePwCIc"></i>
                        </button>
                        <div class="err-msg" id="rPwC-err">Les mots de passe ne correspondent pas.</div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-auth" id="btnReg">
                <span class="spinner" id="regSpinner"></span>
                Créer mon compte
            </button>
        </form>
        <div class="auth-footer">
            Déjà un compte ? <a href="{{ route('login') }}">Se connecter</a>
        </div>
    </div>
</div>

<script>
const lettres=/^[\p{L}\s\-']+$/u;

// Bloquer chiffres dans nom/prénom
['rNom','rPrenom'].forEach(id=>{
    document.getElementById(id).addEventListener('input',function(){
        this.value=this.value.replace(/[0-9]/g,'');
    });
});

// Toggle œil mots de passe
[['eyePw','rPw','eyePwIc'],['eyePwC','rPwC','eyePwCIc']].forEach(([btn,inp,ic])=>{
    document.getElementById(btn).addEventListener('click',()=>{
        const el=document.getElementById(inp),icon=document.getElementById(ic);
        if(el.type==='password'){el.type='text';icon.className='bi bi-eye';}
        else{el.type='password';icon.className='bi bi-eye-slash';}
    });
});

// Indicateur de force du mot de passe
document.getElementById('rPw').addEventListener('input',function(){
    const v=this.value,bar=document.getElementById('pwBar'),txt=document.getElementById('pwText');
    let score=0;
    if(v.length>=8)score++;if(v.length>=12)score++;
    if(/[A-Z]/.test(v))score++;if(/[0-9]/.test(v))score++;if(/[^A-Za-z0-9]/.test(v))score++;
    const levels=[
        {w:'0%',c:'#DDE3EC',t:''},
        {w:'25%',c:'var(--red)',t:'Très faible'},
        {w:'50%',c:'var(--amber)',t:'Faible'},
        {w:'75%',c:'#F59E0B',t:'Moyen'},
        {w:'90%',c:'var(--green)',t:'Fort'},
        {w:'100%',c:'#065F46',t:'Très fort'},
    ];
    const l=levels[Math.min(score,5)];
    bar.style.width=l.w;bar.style.background=l.c;
    txt.textContent=l.t;txt.style.color=l.c;
});

// Validation soumission
document.getElementById('regForm').addEventListener('submit',function(e){
    let ok=true;
    function showErr(id,msg){
        const el=document.getElementById(id),err=document.getElementById(id+'-err');
        el.classList.add('is-invalid');if(err){err.style.display='block';if(msg)err.textContent=msg;}
        ok=false;
    }
    function clearErr(id){
        const el=document.getElementById(id),err=document.getElementById(id+'-err');
        el.classList.remove('is-invalid');if(err)err.style.display='none';
    }
    ['rNom','rPrenom','rEmail','rPw','rPwC'].forEach(clearErr);

    const nom=document.getElementById('rNom').value.trim();
    const prenom=document.getElementById('rPrenom').value.trim();
    const email=document.getElementById('rEmail').value.trim();
    const pw=document.getElementById('rPw').value;
    const pwc=document.getElementById('rPwC').value;

    if(!nom||!lettres.test(nom))showErr('rNom','Nom invalide (lettres uniquement).');
    if(!prenom||!lettres.test(prenom))showErr('rPrenom','Prénom invalide (lettres uniquement).');
    if(!email||!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))showErr('rEmail','Email invalide.');
    if(!pw||pw.length<8)showErr('rPw','Au moins 8 caractères requis.');
    if(pw!==pwc)showErr('rPwC','Les mots de passe ne correspondent pas.');

    if(!ok){e.preventDefault();}else{
        const btn=document.getElementById('btnReg');
        btn.disabled=true;
        document.getElementById('regSpinner').style.display='inline-block';
    }
});
</script>
</body>
</html>
