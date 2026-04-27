
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHUD B/A — Vérification du code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--blue:#005A9C;--blue-d:#003F6E;--blue-l:#E8F3FC;--green:#00875A;--green-l:#E3F5EF;
              --red:#C8001A;--red-l:#FDECEA;--text:#1A2942;--muted:#6B7A8D;}
        *{box-sizing:border-box;margin:0;padding:0}

        /* ── Corps : scroll vertical activé ── */
        html,body{
            font-family:'Plus Jakarta Sans',sans-serif;
            min-height:100vh;
            background:linear-gradient(135deg,var(--blue-d),var(--blue),#0077CC);
            position:relative;overflow-x:hidden;
            /* overflow-y: auto pour permettre le défilement */
            overflow-y:auto;
        }
        body::before{content:'';position:fixed;width:500px;height:500px;border-radius:50%;
            background:rgba(255,255,255,.04);top:-150px;right:-100px;
            animation:float 8s ease-in-out infinite;pointer-events:none}
        body::after{content:'';position:fixed;width:350px;height:350px;border-radius:50%;
            background:rgba(255,255,255,.04);bottom:-100px;left:-80px;
            animation:float 10s ease-in-out infinite reverse;pointer-events:none}

        /* ── Centrage avec padding vertical pour le scroll ── */
        .page-wrap{
            display:flex;align-items:center;justify-content:center;
            min-height:100vh;padding:24px 16px;
        }

        .auth-card{
            background:#fff;border-radius:20px;width:100%;max-width:430px;
            box-shadow:0 20px 60px rgba(0,0,0,.25);
            animation:slideUp .5s ease;overflow:hidden;position:relative;z-index:1;
        }
        .auth-header{
            background:linear-gradient(135deg,var(--blue-d),var(--blue));
            padding:22px 32px;text-align:center
        }
        .auth-logo{width:58px;height:58px;background:#fff;border-radius:13px;margin:0 auto 10px;
            display:flex;align-items:center;justify-content:center;overflow:hidden}
        .auth-logo img{width:90%;height:90%;object-fit:contain}
        .auth-hospital{color:#fff;font-weight:700;font-size:.95rem}
        .auth-subtitle{color:rgba(255,255,255,.6);font-size:.7rem;margin-top:2px}

        .auth-body{padding:24px 32px 28px}

        /* Barre de progression */
        .step-bar{display:flex;gap:6px;margin-bottom:20px}
        .step{height:4px;border-radius:2px;flex:1;transition:background .4s;background:#EEF2F7}
        .step.done{background:var(--green)}.step.active{background:var(--blue)}

        .icon-center{
            width:52px;height:52px;border-radius:50%;background:#E3F5EF;
            color:var(--green);display:flex;align-items:center;justify-content:center;
            font-size:1.4rem;margin:0 auto 14px
        }
        .auth-title{font-weight:700;font-size:1rem;color:var(--text);margin-bottom:4px;text-align:center}
        .auth-desc{font-size:.8rem;color:var(--muted);margin-bottom:20px;line-height:1.6;text-align:center}
        .email-chip{display:inline-block;background:var(--blue-l);color:var(--blue-d);
            border-radius:20px;padding:3px 12px;font-size:.78rem;font-weight:600;margin-top:3px}

        /* 4 cases code */
        .code-inputs{display:flex;gap:8px;justify-content:center;margin-bottom:6px}
        .code-input{
            width:58px;height:62px;border-radius:12px;
            border:2px solid #DDE3EC;font-size:1.7rem;font-weight:700;
            text-align:center;color:var(--blue-d);font-family:'Courier New',monospace;
            transition:all .2s;outline:none;background:#F8FAFD;
        }
        .code-input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(0,90,156,.15);background:#fff}
        .code-input.filled{border-color:var(--blue);background:#E8F3FC}
        .code-input.is-invalid{border-color:var(--red);animation:shake .4s ease}
        @keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}

        .err-msg{font-size:.76rem;color:var(--red);text-align:center;margin-bottom:10px;min-height:18px}

        /* Bouton principal */
        .btn-auth{
            width:100%;padding:11px;border-radius:9px;background:var(--blue);border:none;
            color:#fff;font-size:.88rem;font-weight:600;cursor:pointer;transition:all .25s;
            font-family:inherit;display:flex;align-items:center;justify-content:center;
            gap:8px;margin-top:10px;
        }
        .btn-auth:hover{background:var(--blue-d);transform:translateY(-1px);box-shadow:0 5px 16px rgba(0,90,156,.35)}
        .btn-auth:disabled{opacity:.7;cursor:not-allowed;transform:none}

        /* Timer et renvoi */
        .timer-section{text-align:center;margin-top:14px;font-size:.8rem;color:var(--muted);line-height:1.8}
        .timer{font-weight:700;color:var(--blue)}
        .timer.urgent{color:var(--red)}
        .resend-btn{
            color:var(--blue);text-decoration:none;font-weight:600;cursor:pointer;
            background:none;border:none;font-family:inherit;font-size:.8rem;padding:0;
        }
        .resend-btn:disabled{color:var(--muted);cursor:not-allowed}
        .resend-btn:hover:not(:disabled){text-decoration:underline}

        /* Alertes */
        .alert-auth{border:none;border-radius:9px;font-size:.81rem;padding:9px 13px;margin-bottom:14px}
        .alert-success-auth{background:var(--green-l);color:#00533A}
        .alert-danger-auth{background:var(--red-l);color:#7A000F}

        @keyframes slideUp{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-18px)}}
        @keyframes spin{to{transform:rotate(360deg)}}
        .spinner{
            width:15px;height:15px;border:2px solid rgba(255,255,255,.4);
            border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite;display:none;
        }

        /* Responsive petits écrans */
        @media(max-width:400px){
            .auth-body{padding:20px 20px 24px}
            .code-input{width:52px;height:56px;font-size:1.5rem}
            .code-inputs{gap:6px}
        }
    </style>
</head>
<body>
<div class="page-wrap">
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">
            @if(file_exists(public_path('images/image.png')))
                <img src="{{ asset('images/image.png') }}" alt="Logo CHUD">
            @else
                <i class="bi bi-hospital-fill" style="font-size:1.7rem;color:var(--blue)"></i>
            @endif
        </div>
        <div class="auth-hospital">CHUD Borgou-Alibori</div>
        <div class="auth-subtitle">Réinitialisation du mot de passe</div>
    </div>

    <div class="auth-body">
        {{-- Progression --}}
        <div class="step-bar">
            <div class="step done"></div>
            <div class="step active"></div>
            <div class="step"></div>
        </div>

        <div class="icon-center"><i class="bi bi-shield-lock-fill"></i></div>
        <div class="auth-title">Étape 2 — Entrez le code</div>
        <div class="auth-desc">
            Code envoyé à :<br>
            <span class="email-chip">{{ $email }}</span>
        </div>

        @if(session('status'))
        <div class="alert-auth alert-success-auth">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('status') }}
        </div>
        @endif
        @if($errors->any())
        <div class="alert-auth alert-danger-auth">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('password.verify-code') }}" id="codeForm" novalidate>
            @csrf
            <input type="hidden" name="code" id="codeHidden">

            <div class="code-inputs">
                <input type="text" class="code-input" id="c0" maxlength="1" inputmode="numeric" autocomplete="off">
                <input type="text" class="code-input" id="c1" maxlength="1" inputmode="numeric" autocomplete="off">
                <input type="text" class="code-input" id="c2" maxlength="1" inputmode="numeric" autocomplete="off">
                <input type="text" class="code-input" id="c3" maxlength="1" inputmode="numeric" autocomplete="off">
            </div>
            <div class="err-msg" id="codeErr"></div>

            <button type="submit" class="btn-auth" id="btnVerify">
                <span class="spinner" id="vSpinner"></span>
                <i class="bi bi-check-circle" id="vIcon"></i>
                <span id="vText">Vérifier le code</span>
            </button>
        </form>

        <div class="timer-section">
            <div>Code valide encore : <span class="timer" id="timer">10:00</span></div>
            <div style="margin-top:4px">
                Pas reçu ?&nbsp;
                <form method="POST" action="{{ route('password.resend-code') }}" style="display:inline" id="resendForm">
                    @csrf
                    <button type="submit" class="resend-btn" id="resendBtn" disabled>
                        Renvoyer le code
                    </button>
                </form>
                <div class="auth-footer"><a href="{{ route('login') }}"><i class="bi bi-arrow-left me-1"></i>Retour à la connexion</a></div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
const inputs=[0,1,2,3].map(i=>document.getElementById('c'+i));

inputs.forEach((inp,idx)=>{
    inp.addEventListener('keydown',function(e){
        if(e.key==='Backspace'){
            this.value='';this.classList.remove('filled');
            if(idx>0)inputs[idx-1].focus();
            return;
        }
        if(!/^[0-9]$/.test(e.key)&&e.key!=='Tab')e.preventDefault();
    });
    inp.addEventListener('input',function(){
        if(this.value&&/^[0-9]$/.test(this.value)){
            this.classList.add('filled');
            if(idx<3)inputs[idx+1].focus();
            else document.getElementById('btnVerify').focus();
        }
    });
    inp.addEventListener('paste',function(e){
        e.preventDefault();
        const p=(e.clipboardData||window.clipboardData).getData('text').replace(/\D/g,'').slice(0,4);
        p.split('').forEach((ch,i)=>{if(inputs[i]){inputs[i].value=ch;inputs[i].classList.add('filled');}});
        if(p.length===4)document.getElementById('btnVerify').focus();
    });
});
inputs[0].focus();

document.getElementById('codeForm').addEventListener('submit',function(e){
    const code=inputs.map(i=>i.value).join('');
    const errDiv=document.getElementById('codeErr');
    errDiv.textContent='';
    inputs.forEach(i=>i.classList.remove('is-invalid'));
    if(code.length<4||!/^\d{4}$/.test(code)){
        inputs.forEach(i=>{if(!i.value)i.classList.add('is-invalid');});
        errDiv.textContent='Veuillez entrer les 4 chiffres du code.';
        e.preventDefault();return;
    }
    document.getElementById('codeHidden').value=code;
    const btn=document.getElementById('btnVerify');
    btn.disabled=true;
    document.getElementById('vSpinner').style.display='inline-block';
    document.getElementById('vIcon').style.display='none';
    document.getElementById('vText').textContent='Vérification…';
});

// Minuteur 10min
let secs=600;
const timerEl=document.getElementById('timer');
const resendBtn=document.getElementById('resendBtn');
const iv=setInterval(()=>{
    secs--;
    const m=Math.floor(secs/60),s=secs%60;
    timerEl.textContent=m+':'+(s<10?'0':'')+s;
    if(secs<=60)timerEl.classList.add('urgent');
    if(secs<=0){clearInterval(iv);timerEl.textContent='Expiré';resendBtn.disabled=false;}
},1000);
setTimeout(()=>{resendBtn.disabled=false;},30000);
</script>
</body>
</html>

