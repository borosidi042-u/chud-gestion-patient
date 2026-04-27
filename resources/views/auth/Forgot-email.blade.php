<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHUD B/A — Mot de passe oublié</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--blue:#005A9C;--blue-d:#003F6E;--blue-l:#E8F3FC;--green:#00875A;--green-l:#E3F5EF;
              --red:#C8001A;--red-l:#FDECEA;--text:#1A2942;--muted:#6B7A8D;}
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Plus Jakarta Sans',sans-serif;min-height:100vh;
            background:linear-gradient(135deg,var(--blue-d),var(--blue),#0077CC);
            display:flex;align-items:center;justify-content:center;padding:20px;position:relative;overflow:hidden}
        body::before{content:'';position:absolute;width:500px;height:500px;border-radius:50%;
            background:rgba(255,255,255,.04);top:-150px;right:-100px;animation:float 8s ease-in-out infinite}
        body::after{content:'';position:absolute;width:350px;height:350px;border-radius:50%;
            background:rgba(255,255,255,.04);bottom:-100px;left:-80px;animation:float 10s ease-in-out infinite reverse}
        .auth-card{background:#fff;border-radius:20px;width:100%;max-width:430px;
            box-shadow:0 20px 60px rgba(0,0,0,.25);animation:slideUp .5s ease;overflow:hidden;position:relative;z-index:1}
        .auth-header{background:linear-gradient(135deg,var(--blue-d),var(--blue));padding:26px 36px;text-align:center}
        .auth-logo{width:64px;height:64px;background:#fff;border-radius:14px;margin:0 auto 12px;
            display:flex;align-items:center;justify-content:center;overflow:hidden}
        .auth-logo img{width:90%;height:90%;object-fit:contain}
        .auth-hospital{color:#fff;font-weight:700;font-size:1rem}
        .auth-subtitle{color:rgba(255,255,255,.6);font-size:.73rem;margin-top:3px}
        .auth-body{padding:28px 36px 32px}
        .step-bar{display:flex;gap:6px;margin-bottom:22px}
        .step{height:4px;border-radius:2px;flex:1;background:#EEF2F7;transition:background .4s}
        .step.active{background:var(--blue)}.step.done{background:var(--green)}
        .icon-center{width:58px;height:58px;border-radius:50%;background:var(--blue-l);
            color:var(--blue);display:flex;align-items:center;justify-content:center;
            font-size:1.5rem;margin:0 auto 16px}
        .auth-title{font-weight:700;font-size:1.05rem;color:var(--text);margin-bottom:5px;text-align:center}
        .auth-desc{font-size:.82rem;color:var(--muted);margin-bottom:22px;line-height:1.6;text-align:center}
        .form-label{font-weight:600;font-size:.8rem;color:var(--text);margin-bottom:5px;display:block}
        .input-wrap{position:relative;margin-bottom:6px}
        .input-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.9rem;pointer-events:none}
        .form-control{width:100%;border-radius:9px;border:1.5px solid #DDE3EC;
            font-size:.875rem;padding:10px 14px 10px 38px;transition:all .2s;font-family:inherit;color:var(--text)}
        .form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(0,90,156,.12);outline:none}
        .form-control.is-invalid{border-color:var(--red)}
        .err-msg{font-size:.76rem;color:var(--red);margin-top:4px;display:none}
        .btn-auth{width:100%;padding:11px;border-radius:9px;background:var(--blue);border:none;
            color:#fff;font-size:.9rem;font-weight:600;cursor:pointer;transition:all .25s;
            font-family:inherit;margin-top:10px;display:flex;align-items:center;justify-content:center;gap:8px}
        .btn-auth:hover{background:var(--blue-d);transform:translateY(-1px);box-shadow:0 6px 18px rgba(0,90,156,.35)}
        .btn-auth:disabled{opacity:.7;cursor:not-allowed;transform:none}
        .back-link{display:block;text-align:center;margin-top:16px;font-size:.82rem;color:var(--blue);text-decoration:none;font-weight:500}
        .back-link:hover{text-decoration:underline}
        .alert-auth{border:none;border-radius:9px;font-size:.83rem;padding:10px 14px;margin-bottom:16px}
        .alert-success-auth{background:var(--green-l);color:#00533A}
        .alert-danger-auth{background:var(--red-l);color:#7A000F}
        @keyframes slideUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-20px)}}
        @keyframes spin{to{transform:rotate(360deg)}}
        .spinner{width:16px;height:16px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite;display:none}
    </style>
</head>
<body>
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">
            @if(file_exists(public_path('images/image.png')))
                <img src="{{ asset('images/image.png') }}" alt="Logo CHUD">
            @else
                <i class="bi bi-hospital-fill" style="font-size:1.8rem;color:var(--blue)"></i>
            @endif
        </div>
        <div class="auth-hospital">CHUD Borgou-Alibori</div>
        <div class="auth-subtitle">Réinitialisation du mot de passe</div>
    </div>
    <div class="auth-body">
        {{-- Barre de progression étapes --}}
        <div class="step-bar">
            <div class="step active"></div>
            <div class="step"></div>
            <div class="step"></div>
        </div>
        <div class="icon-center"><i class="bi bi-envelope-at"></i></div>
        <div class="auth-title">Étape 1 — Votre email</div>
        <div class="auth-desc">Entrez votre adresse email. Vous recevrez un <strong>code à 4 chiffres</strong> valable 10 minutes.</div>

        @if(session('status'))
        <div class="alert-auth alert-success-auth"><i class="bi bi-check-circle-fill me-1"></i>{{ session('status') }}</div>
        @endif
        @if($errors->any())
        <div class="alert-auth alert-danger-auth"><i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.send-code') }}" id="emailForm" novalidate>
            @csrf
            <label class="form-label">Adresse email</label>
            <div class="input-wrap">
                <i class="bi bi-envelope input-icon"></i>
                <input type="email" name="email" id="fpEmail" value="{{ old('email') }}"
                       class="form-control" placeholder="votre@email.bj" required autocomplete="email">
                <div class="err-msg" id="fpEmail-err">Email invalide.</div>
            </div>
            <button type="submit" class="btn-auth" id="btnSend">
                <span class="spinner" id="btnSpinner"></span>
                <i class="bi bi-send" id="btnIcon"></i>
                <span id="btnText">Envoyer le code</span>
            </button>
        </form>
        <a href="{{ route('login') }}" class="back-link"><i class="bi bi-arrow-left me-1"></i>Retour à la connexion</a>
    </div>
</div>
<script>
document.getElementById('emailForm').addEventListener('submit',function(e){
    const em=document.getElementById('fpEmail'),err=document.getElementById('fpEmail-err');
    em.classList.remove('is-invalid');err.style.display='none';
    if(!em.value.trim()||!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em.value)){
        em.classList.add('is-invalid');err.textContent='Veuillez entrer un email valide.';
        err.style.display='block';e.preventDefault();return;
    }
    const btn=document.getElementById('btnSend');
    btn.disabled=true;
    document.getElementById('btnSpinner').style.display='inline-block';
    document.getElementById('btnIcon').style.display='none';
    document.getElementById('btnText').textContent='Envoi en cours…';
});
</script>
</body>
</html>