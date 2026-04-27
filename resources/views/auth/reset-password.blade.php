<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHUD B/A — Nouveau mot de passe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--blue:#005A9C;--blue-d:#003F6E;--blue-l:#E8F3FC;--green:#00875A;--red:#C8001A;--red-l:#FDECEA;--text:#1A2942;--muted:#6B7A8D;}
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Plus Jakarta Sans',sans-serif;min-height:100vh;
            background:linear-gradient(135deg,var(--blue-d),var(--blue),#0077CC);
            display:flex;align-items:center;justify-content:center;padding:20px;overflow:hidden}
        body::before{content:'';position:absolute;width:400px;height:400px;border-radius:50%;
            background:rgba(255,255,255,.04);top:-100px;right:-80px;animation:float 8s ease-in-out infinite}
        .auth-card{background:#fff;border-radius:20px;width:100%;max-width:430px;
            box-shadow:0 20px 60px rgba(0,0,0,.25);animation:slideUp .5s ease;overflow:hidden;position:relative;z-index:1}
        .auth-header{background:linear-gradient(135deg,var(--blue-d),var(--blue));padding:26px 36px;text-align:center}
        .auth-logo{width:60px;height:60px;background:#fff;border-radius:13px;margin:0 auto 12px;
            display:flex;align-items:center;justify-content:center;overflow:hidden}
        .auth-logo img{width:90%;height:90%;object-fit:contain}
        .auth-hospital{color:#fff;font-weight:700;font-size:1rem}
        .auth-subtitle{color:rgba(255,255,255,.6);font-size:.73rem;margin-top:3px}
        .auth-body{padding:28px 36px 32px}
        .auth-title{font-weight:700;font-size:1.05rem;color:var(--text);margin-bottom:4px}
        .auth-desc{font-size:.81rem;color:var(--muted);margin-bottom:20px}
        .form-label{font-weight:600;font-size:.79rem;color:var(--text);margin-bottom:4px;display:block}
        .input-wrap{position:relative;margin-bottom:14px}
        .input-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.9rem;pointer-events:none}
        .form-control{width:100%;border-radius:9px;border:1.5px solid #DDE3EC;
            font-size:.875rem;padding:9px 38px 9px 36px;transition:all .2s;font-family:inherit;color:var(--text)}
        .form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(0,90,156,.12);outline:none}
        .form-control.is-invalid{border-color:var(--red)}
        .form-control.is-valid{border-color:var(--green)}
        .eye-toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);border:none;background:none;color:var(--muted);cursor:pointer;font-size:.9rem;padding:0}
        .err-msg{font-size:.75rem;color:var(--red);margin-top:3px;display:none}
        .pw-strength{height:4px;border-radius:2px;margin-top:6px;background:#DDE3EC}
        .pw-strength-bar{height:100%;border-radius:2px;transition:all .3s;width:0}
        .pw-strength-text{font-size:.72rem;margin-top:3px}
        .btn-auth{width:100%;padding:11px;border-radius:9px;background:var(--blue);border:none;
            color:#fff;font-size:.9rem;font-weight:600;cursor:pointer;transition:all .25s;font-family:inherit;margin-top:4px}
        .btn-auth:hover{background:var(--blue-d);transform:translateY(-1px)}
        .auth-footer{text-align:center;margin-top:16px;font-size:.81rem;color:var(--muted)}
        .auth-footer a{color:var(--blue);text-decoration:none;font-weight:600}
        .alert-auth{border:none;border-radius:9px;font-size:.83rem;padding:10px 14px;margin-bottom:16px;background:var(--red-l);color:#7A000F}
        @keyframes slideUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-20px)}}
    </style>
</head>
<body>
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">
            @if(file_exists(public_path('images/image.png')))
                <img src="{{ asset('images/image.png') }}" alt="Logo">
            @else
                <i class="bi bi-hospital-fill" style="font-size:1.7rem;color:var(--blue)"></i>
            @endif
        </div>
        <div class="auth-hospital">CHUD Borgou-Alibori</div>
        <div class="auth-subtitle">Nouveau mot de passe</div>
    </div>
    <div class="auth-body">
        <div class="auth-title">Réinitialiser le mot de passe</div>
        <div class="auth-desc">Choisissez un nouveau mot de passe sécurisé pour votre compte.</div>

        @if($errors->any())
        <div class="alert-auth"><i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" id="rpForm" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div>
                <label class="form-label">Email</label>
                <div class="input-wrap">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" name="email" class="form-control" value="{{ old('email',$email??'') }}" required readonly style="background:#F5F8FC">
                </div>
            </div>
            <div>
                <label class="form-label">Nouveau mot de passe <span style="color:var(--red)">*</span></label>
                <div class="input-wrap" style="margin-bottom:0">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" name="password" id="rpPw" class="form-control" placeholder="Min. 8 caractères" required>
                    <button type="button" class="eye-toggle" onclick="toggleEye('rpPw','rpEye')"><i id="rpEye" class="bi bi-eye-slash"></i></button>
                    <div class="err-msg" id="rpPw-err">Au moins 8 caractères.</div>
                </div>
                <div class="pw-strength"><div class="pw-strength-bar" id="rpBar"></div></div>
                <div class="pw-strength-text" id="rpTxt" style="margin-bottom:10px"></div>
            </div>
            <div>
                <label class="form-label">Confirmer le mot de passe <span style="color:var(--red)">*</span></label>
                <div class="input-wrap">
                    <i class="bi bi-lock-fill input-icon"></i>
                    <input type="password" name="password_confirmation" id="rpPwC" class="form-control" placeholder="Répétez" required>
                    <button type="button" class="eye-toggle" onclick="toggleEye('rpPwC','rpCEye')"><i id="rpCEye" class="bi bi-eye-slash"></i></button>
                    <div class="err-msg" id="rpPwC-err">Les mots de passe ne correspondent pas.</div>
                </div>
            </div>
            <button type="submit" class="btn-auth" id="rpBtn"><i class="bi bi-shield-lock me-1"></i>Réinitialiser</button>
        </form>
        <div class="auth-footer"><a href="{{ route('login') }}"><i class="bi bi-arrow-left me-1"></i>Retour à la connexion</a></div>
    </div>
</div>
<script>
function toggleEye(inp,ic){const e=document.getElementById(inp),i=document.getElementById(ic);e.type=e.type==='password'?'text':'password';i.className=e.type==='password'?'bi bi-eye-slash':'bi bi-eye';}
document.getElementById('rpPw').addEventListener('input',function(){
    const v=this.value,bar=document.getElementById('rpBar'),txt=document.getElementById('rpTxt');
    let s=0;if(v.length>=8)s++;if(v.length>=12)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;
    const l=[{w:'0%',c:'#DDE3EC',t:''},{w:'25%',c:'var(--red)',t:'Très faible'},{w:'50%',c:'#F59E0B',t:'Faible'},{w:'75%',c:'#D97706',t:'Moyen'},{w:'90%',c:'var(--green)',t:'Fort'},{w:'100%',c:'#065F46',t:'Très fort'}];
    const d=l[Math.min(s,5)];bar.style.width=d.w;bar.style.background=d.c;txt.textContent=d.t;txt.style.color=d.c;
});
document.getElementById('rpForm').addEventListener('submit',function(e){
    let ok=true;
    const pw=document.getElementById('rpPw'),pwc=document.getElementById('rpPwC');
    const err1=document.getElementById('rpPw-err'),err2=document.getElementById('rpPwC-err');
    [pw,pwc].forEach(el=>{el.classList.remove('is-invalid');});[err1,err2].forEach(el=>{el.style.display='none';});
    if(!pw.value||pw.value.length<8){pw.classList.add('is-invalid');err1.style.display='block';ok=false;}
    if(pw.value!==pwc.value){pwc.classList.add('is-invalid');err2.style.display='block';ok=false;}
    if(!ok)e.preventDefault();else{document.getElementById('rpBtn').disabled=true;}
});
</script>
</body>
</html>
