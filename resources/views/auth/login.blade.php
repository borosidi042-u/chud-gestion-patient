<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHUD B/A — Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--blue:#005A9C;--blue-d:#003F6E;--blue-l:#E8F3FC;--green:#00875A;--green-l:#E3F5EF;
              --red:#C8001A;--red-l:#FDECEA;--text:#1A2942;--muted:#6B7A8D;}
        *{box-sizing:border-box;margin:0;padding:0}
        
        /* Corps avec défilement autorisé */
        body{font-family:'Plus Jakarta Sans',sans-serif;min-height:100vh;
            background:linear-gradient(135deg,var(--blue-d) 0%,var(--blue) 50%,#0077CC 100%);
            display:flex;align-items:center;justify-content:center;
            padding:40px 20px;  /* Augmenté le padding vertical */
            position:relative;overflow-y:auto;  /* Défilement vertical autorisé */
            overflow-x:hidden;
        }

        /* Cercles décoratifs animés */
        body::before{content:'';position:fixed;width:500px;height:500px;
            border-radius:50%;background:rgba(255,255,255,.04);
            top:-150px;right:-100px;animation:float 8s ease-in-out infinite;pointer-events:none;}
        body::after{content:'';position:fixed;width:350px;height:350px;
            border-radius:50%;background:rgba(255,255,255,.04);
            bottom:-100px;left:-80px;animation:float 10s ease-in-out infinite reverse;pointer-events:none;}
        .deco-circle{position:fixed;width:200px;height:200px;border-radius:50%;
            background:rgba(255,255,255,.06);top:40%;left:10%;animation:float 7s ease-in-out infinite 2s;pointer-events:none;}

        /* Carte avec hauteur automatique et défilement interne si nécessaire */
        .auth-card{background:#fff;border-radius:20px;padding:0;width:100%;max-width:440px;
            box-shadow:0 20px 60px rgba(0,0,0,.25);animation:slideUp .5s ease;
            overflow-y:auto;  /* Permet le défilement interne si besoin */
            overflow-x:hidden;
            position:relative;z-index:1;
            max-height:90vh;  /* Hauteur maximale de 90% de l'écran */
        }
        
        /* Personnalisation de la barre de défilement */
        .auth-card::-webkit-scrollbar {
            width: 6px;
        }
        .auth-card::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .auth-card::-webkit-scrollbar-thumb {
            background: var(--blue);
            border-radius: 10px;
        }
        .auth-card::-webkit-scrollbar-thumb:hover {
            background: var(--blue-d);
        }
        
        .auth-header{background:linear-gradient(135deg,var(--blue-d),var(--blue));
            padding:32px 36px 28px;text-align:center;}
        .auth-logo{width:80px;height:80px;background:#fff;border-radius:16px;
            margin:0 auto 16px;display:flex;align-items:center;justify-content:center;
            overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.15)}
        .auth-logo img{width:90%;height:90%;object-fit:contain}
        .auth-logo-fallback{font-size:2rem;color:var(--blue)}
        .auth-hospital{color:#fff;font-weight:700;font-size:1.15rem;letter-spacing:.3px}
        .auth-subtitle{color:rgba(255,255,255,.65);font-size:.78rem;margin-top:4px}
        .auth-body{padding:32px 36px}
        .auth-title{font-weight:700;font-size:1.1rem;color:var(--text);margin-bottom:6px}
        .auth-desc{font-size:.82rem;color:var(--muted);margin-bottom:24px}

        .form-label{font-weight:600;font-size:.8rem;color:var(--text);margin-bottom:5px;display:block}
        .input-wrap{position:relative;margin-bottom:16px}
        .input-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);
            color:var(--muted);font-size:.95rem;pointer-events:none}
        .form-control{width:100%;border-radius:9px;border:1.5px solid #DDE3EC;
            font-size:.875rem;padding:10px 14px 10px 38px;transition:all .2s;
            font-family:inherit;color:var(--text)}
        .form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(0,90,156,.12);outline:none}
        .form-control.is-invalid{border-color:var(--red)}
        .form-control.is-invalid:focus{box-shadow:0 0 0 3px rgba(200,0,26,.12)}
        .eye-toggle{position:absolute;right:13px;top:50%;transform:translateY(-50%);
            border:none;background:none;color:var(--muted);cursor:pointer;font-size:.95rem;padding:0}
        .eye-toggle:hover{color:var(--blue)}
        .err-msg{font-size:.77rem;color:var(--red);margin-top:4px}
        .field-hint{font-size:.75rem;color:var(--muted);margin-top:3px}

        .btn-auth{width:100%;padding:11px;border-radius:9px;background:var(--blue);
            border:none;color:#fff;font-size:.9rem;font-weight:600;cursor:pointer;
            transition:all .25s;font-family:inherit;margin-top:8px}
        .btn-auth:hover{background:var(--blue-d);transform:translateY(-1px);
            box-shadow:0 6px 18px rgba(0,90,156,.35)}
        .btn-auth:active{transform:translateY(0)}
        .btn-auth:disabled{opacity:.7;cursor:not-allowed;transform:none}

        .auth-footer{text-align:center;margin-top:20px;margin-bottom:10px;font-size:.82rem;color:var(--muted)}
        .auth-footer a{color:var(--blue);text-decoration:none;font-weight:600}
        .auth-footer a:hover{text-decoration:underline}
        .forgot-link{display:block;text-align:right;font-size:.78rem;color:var(--blue);
            text-decoration:none;margin-top:-8px;margin-bottom:16px;font-weight:500}
        .forgot-link:hover{text-decoration:underline}
        .checkbox-wrap{display:flex;align-items:center;gap:8px;margin-bottom:16px}
        .checkbox-wrap input{width:16px;height:16px;cursor:pointer;accent-color:var(--blue)}
        .checkbox-wrap label{font-size:.82rem;color:var(--muted);cursor:pointer;margin:0}

        .alert-auth{border:none;border-radius:9px;font-size:.83rem;padding:10px 14px;margin-bottom:16px}
        .alert-danger-auth{background:var(--red-l);color:#7A000F}
        .alert-success-auth{background:var(--green-l);color:#00533A}

        @keyframes slideUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-20px)}}
        @keyframes spin{to{transform:rotate(360deg)}}
        .spinner{display:none;width:16px;height:16px;border:2px solid rgba(255,255,255,.4);
            border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite;
            vertical-align:middle;margin-right:6px}
        .loading .spinner{display:inline-block}
        .loading .btn-text{display:none}
        
        /* Responsive pour les petits écrans */
        @media (max-width: 480px) {
            .auth-body {
                padding: 24px 20px;
            }
            .auth-header {
                padding: 24px 20px 20px;
            }
            .auth-card {
                max-height: 95vh;
            }
            body {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
<div class="deco-circle"></div>
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">
            @php
                $logoPath = public_path('images/image.png');
                $hasLogo = file_exists($logoPath);
            @endphp
            @if($hasLogo)
                <img src="{{ asset('images/image.png') }}" alt="Logo CHUD-BA">
            @else
                <i class="bi bi-hospital-fill auth-logo-fallback"></i>
            @endif
        </div>
        <div class="auth-hospital">CHUD Borgou-Alibori</div>
        <div class="auth-subtitle">Centre Hospitalier Universitaire Départemental</div>
    </div>
    <div class="auth-body">
        <div class="auth-title">Connexion</div>
        <div class="auth-desc">Accédez à votre espace de gestion des patients.</div>

        @if($errors->any())
        <div class="alert-auth alert-danger-auth">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            {{ $errors->first() }}
        </div>
        @endif
        @if(session('status'))
        <div class="alert-auth alert-success-auth">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
            @csrf
            <div>
                <label class="form-label">Adresse email</label>
                <div class="input-wrap">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="form-control" placeholder="ex: agent@chud-ba.bj" required autocomplete="email">
                    <div class="err-msg" id="email-err" style="display:none"></div>
                </div>
            </div>
            <div>
                <label class="form-label">Mot de passe</label>
                <div class="input-wrap" style="margin-bottom:8px">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" name="password" id="password"
                           class="form-control" placeholder="••••••••" required autocomplete="current-password">
                    <button type="button" class="eye-toggle" id="eyeToggle" tabindex="-1">
                        <i class="bi bi-eye-slash" id="eyeIcon"></i>
                    </button>
                    <div class="err-msg" id="pw-err" style="display:none"></div>
                </div>
            </div>
            <a href="{{ route('password.request') }}" class="forgot-link">Mot de passe oublié ?</a>
            <div class="checkbox-wrap">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">Se souvenir de moi</label>
            </div>
            <button type="submit" class="btn-auth" id="btnLogin">
                <span class="spinner" id="spinner"></span>
                <span class="btn-text">Se connecter</span>
            </button>
        </form>
        <div class="auth-footer">
            Pas encore de compte ?
            <a href="{{ route('register') }}">Créer un compte</a>
        </div>
    </div>
</div>

<script>
// Afficher/masquer mot de passe
const eyeToggle = document.getElementById('eyeToggle');
if (eyeToggle) {
    eyeToggle.addEventListener('click', function() {
        const pw = document.getElementById('password');
        const ic = document.getElementById('eyeIcon');
        if (pw.type === 'password') {
            pw.type = 'text';
            ic.className = 'bi bi-eye';
        } else {
            pw.type = 'password';
            ic.className = 'bi bi-eye-slash';
        }
    });
}

// Validation + spinner
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
        let ok = true;
        const email = document.getElementById('email');
        const pw = document.getElementById('password');
        const emailErr = document.getElementById('email-err');
        const pwErr = document.getElementById('pw-err');

        if (emailErr) emailErr.style.display = 'none';
        if (pwErr) pwErr.style.display = 'none';
        if (email) email.classList.remove('is-invalid');
        if (pw) pw.classList.remove('is-invalid');

        if (email && (!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value))) {
            email.classList.add('is-invalid');
            if (emailErr) {
                emailErr.textContent = 'Veuillez entrer un email valide.';
                emailErr.style.display = 'block';
            }
            ok = false;
        }
        if (pw && (!pw.value || pw.value.length < 6)) {
            pw.classList.add('is-invalid');
            if (pwErr) {
                pwErr.textContent = 'Le mot de passe doit contenir au moins 6 caractères.';
                pwErr.style.display = 'block';
            }
            ok = false;
        }
        if (ok) {
            const btn = document.getElementById('btnLogin');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('loading');
            }
            const spinner = document.getElementById('spinner');
            if (spinner) spinner.style.display = 'inline-block';
        } else {
            e.preventDefault();
        }
    });
}
</script>
</body>
</html>