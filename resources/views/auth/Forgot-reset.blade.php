<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>CHUD B/A — Nouveau mot de passe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --blue: #005A9C;
            --blue-d: #003F6E;
            --blue-l: #E8F3FC;
            --green: #00875A;
            --green-l: #E3F5EF;
            --red: #C8001A;
            --red-l: #FDECEA;
            --text: #1A2942;
            --muted: #6B7A8D;
            --amber: #D97706;
            --amber-l: #FFF3E0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--blue-d), var(--blue), #0077CC);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-y: auto;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
            top: -150px;
            right: -100px;
            animation: float 8s ease-in-out infinite;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: fixed;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
            bottom: -100px;
            left: -80px;
            animation: float 10s ease-in-out infinite reverse;
            pointer-events: none;
        }

        .auth-card {
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 430px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.5s ease;
            overflow: hidden;
            position: relative;
            z-index: 1;
            margin: 20px auto;
        }

        .auth-header {
            background: linear-gradient(135deg, var(--blue-d), var(--blue));
            padding: 26px 36px;
            text-align: center;
        }

        .auth-logo {
            width: 64px;
            height: 64px;
            background: #fff;
            border-radius: 14px;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .auth-logo img {
            width: 90%;
            height: 90%;
            object-fit: contain;
        }

        .auth-hospital {
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
        }

        .auth-subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.73rem;
            margin-top: 3px;
        }

        .auth-body {
            padding: 28px 36px 32px;
            max-height: calc(100vh - 180px);
            overflow-y: auto;
        }

        /* Personnalisation du scroll */
        .auth-body::-webkit-scrollbar {
            width: 6px;
        }

        .auth-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .auth-body::-webkit-scrollbar-thumb {
            background: var(--blue);
            border-radius: 3px;
        }

        .auth-body::-webkit-scrollbar-thumb:hover {
            background: var(--blue-d);
        }

        .step-bar {
            display: flex;
            gap: 6px;
            margin-bottom: 22px;
        }

        .step {
            height: 4px;
            border-radius: 2px;
            flex: 1;
            transition: background 0.4s;
            background: #EEF2F7;
        }

        .step.done {
            background: var(--green);
        }

        .step.active {
            background: var(--blue);
        }

        .icon-center {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: var(--amber-l);
            color: var(--amber);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 16px;
        }

        .auth-title {
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--text);
            margin-bottom: 5px;
            text-align: center;
        }

        .auth-desc {
            font-size: 0.82rem;
            color: var(--muted);
            margin-bottom: 22px;
            line-height: 1.6;
            text-align: center;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--text);
            margin-bottom: 5px;
            display: block;
        }

        .input-wrap {
            position: relative;
            margin-bottom: 14px;
        }

        .input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 0.9rem;
            pointer-events: none;
            z-index: 1;
        }

        .form-control {
            width: 100%;
            border-radius: 9px;
            border: 1.5px solid #DDE3EC;
            font-size: 0.875rem;
            padding: 10px 40px 10px 38px;
            transition: all 0.2s;
            font-family: inherit;
            color: var(--text);
        }

        .form-control:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(0, 90, 156, 0.12);
            outline: none;
        }

        .form-control.is-invalid {
            border-color: var(--red);
        }

        .form-control.is-valid {
            border-color: var(--green);
        }

        .eye-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 0.9rem;
            padding: 5px;
            z-index: 1;
        }

        .eye-btn:hover {
            color: var(--blue);
        }

        .err-msg {
            font-size: 0.76rem;
            color: var(--red);
            margin-top: 3px;
            display: none;
        }

        .pw-bar-wrap {
            height: 4px;
            border-radius: 2px;
            background: #EEF2F7;
            margin-top: 6px;
            overflow: hidden;
        }

        .pw-bar {
            height: 100%;
            border-radius: 2px;
            transition: all 0.3s;
            width: 0;
        }

        .pw-lbl {
            font-size: 0.72rem;
            margin-top: 3px;
            margin-bottom: 12px;
        }

        .btn-auth {
            width: 100%;
            padding: 11px;
            border-radius: 9px;
            background: var(--blue);
            border: none;
            color: #fff;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s;
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
        }

        .btn-auth:hover {
            background: var(--blue-d);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(0, 90, 156, 0.35);
        }

        .btn-auth:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .alert-auth {
            border: none;
            border-radius: 9px;
            font-size: 0.83rem;
            padding: 10px 14px;
            margin-bottom: 16px;
        }

        .alert-danger-auth {
            background: var(--red-l);
            color: #7A000F;
        }

        .alert-success-auth {
            background: var(--green-l);
            color: #00533A;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            display: none;
        }

        /* Responsive */
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .auth-card {
                max-width: 100%;
                margin: 10px auto;
            }

            .auth-header {
                padding: 20px 24px;
            }

            .auth-body {
                padding: 20px 24px 28px;
            }

            .auth-logo {
                width: 50px;
                height: 50px;
            }

            .icon-center {
                width: 48px;
                height: 48px;
                font-size: 1.2rem;
            }

            .form-control {
                font-size: 0.8rem;
                padding: 8px 36px 8px 34px;
            }
        }

        @media (max-width: 360px) {
            .auth-body {
                padding: 16px 20px 24px;
            }

            .auth-title {
                font-size: 0.95rem;
            }

            .auth-desc {
                font-size: 0.75rem;
            }
            .auth-footer{text-align: center; bottom: 25%;}
        }
    </style>
</head>
<body>
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">
            @if(file_exists(public_path('images/image.png')))
                <img src="{{ asset('images/image.png') }}" alt="Logo">
            @else
                <i class="bi bi-hospital-fill" style="font-size:1.8rem;color:var(--blue)"></i>
            @endif
        </div>
        <div class="auth-hospital">CHUD Borgou-Alibori</div>
        <div class="auth-subtitle">Réinitialisation du mot de passe</div>
    </div>
    <div class="auth-body">
        <div class="step-bar">
            <div class="step done"></div>
            <div class="step done"></div>
            <div class="step active"></div>
        </div>
        <div class="icon-center">
            <i class="bi bi-key-fill"></i>
        </div>
        <div class="auth-title">Étape 3 — Nouveau mot de passe</div>
        <div class="auth-desc">Choisissez un nouveau mot de passe sécurisé pour votre compte.</div>

        @if(session('status'))
        <div class="alert-auth alert-success-auth">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('status') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert-auth alert-danger-auth">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            @foreach($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('password.update-new') }}" id="rpForm" novalidate>
            @csrf

            <div>
                <label class="form-label">
                    Nouveau mot de passe <span style="color:var(--red)">*</span>
                </label>
                <div class="input-wrap">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" name="password" id="rpPw"
                           class="form-control" placeholder="Min. 8 caractères" required>
                    <button type="button" class="eye-btn" onclick="toggleEye('rpPw','rpEyeIc')">
                        <i id="rpEyeIc" class="bi bi-eye-slash"></i>
                    </button>
                    <div class="err-msg" id="rpPw-err">Au moins 8 caractères requis.</div>
                </div>
                <div class="pw-bar-wrap">
                    <div class="pw-bar" id="pwBar"></div>
                </div>
                <div class="pw-lbl" id="pwLbl"></div>
            </div>

            <div>
                <label class="form-label">
                    Confirmer le mot de passe <span style="color:var(--red)">*</span>
                </label>
                <div class="input-wrap">
                    <i class="bi bi-lock-fill input-icon"></i>
                    <input type="password" name="password_confirmation" id="rpPwC"
                           class="form-control" placeholder="Répétez le mot de passe" required>
                    <button type="button" class="eye-btn" onclick="toggleEye('rpPwC','rpCEyeIc')">
                        <i id="rpCEyeIc" class="bi bi-eye-slash"></i>
                    </button>
                    <div class="err-msg" id="rpPwC-err">Les mots de passe ne correspondent pas.</div>
                </div>
            </div>

            <button type="submit" class="btn-auth" id="rpBtn">
                <span class="spinner" id="rpSpinner"></span>
                <i class="bi bi-shield-check" id="rpIcon"></i>
                <span id="rpText">Enregistrer le mot de passe</span>
            </button>
        </form>
        <div class="auth-footer"><a href="{{ route('login') }}"><i class="bi bi-arrow-left me-1"></i>Retour à la connexion</a></div>
    </div>
</div>

<script>
function toggleEye(inpId, icId) {
    const el = document.getElementById(inpId);
    const ic = document.getElementById(icId);
    if (el && ic) {
        if (el.type === 'password') {
            el.type = 'text';
            ic.className = 'bi bi-eye';
        } else {
            el.type = 'password';
            ic.className = 'bi bi-eye-slash';
        }
    }
}

// Indicateur de force du mot de passe
const rpPw = document.getElementById('rpPw');
if (rpPw) {
    rpPw.addEventListener('input', function() {
        const v = this.value;
        const bar = document.getElementById('pwBar');
        const lbl = document.getElementById('pwLbl');

        if (!bar || !lbl) return;

        let score = 0;
        if (v.length >= 8) score++;
        if (v.length >= 12) score++;
        if (/[A-Z]/.test(v)) score++;
        if (/[0-9]/.test(v)) score++;
        if (/[^A-Za-z0-9]/.test(v)) score++;

        const levels = [
            { width: '0%', color: '#EEF2F7', text: '' },
            { width: '20%', color: '#C8001A', text: 'Très faible' },
            { width: '40%', color: '#F59E0B', text: 'Faible' },
            { width: '65%', color: '#D97706', text: 'Moyen' },
            { width: '85%', color: '#00875A', text: 'Fort' },
            { width: '100%', color: '#065F46', text: 'Très fort' }
        ];

        const level = levels[Math.min(score, 5)];
        bar.style.width = level.width;
        bar.style.backgroundColor = level.color;
        lbl.textContent = level.text;
        lbl.style.color = level.color;
    });
}

// Validation du formulaire
const rpForm = document.getElementById('rpForm');
if (rpForm) {
    rpForm.addEventListener('submit', function(e) {
        let ok = true;
        const pw = document.getElementById('rpPw');
        const pwc = document.getElementById('rpPwC');
        const errPw = document.getElementById('rpPw-err');
        const errPwc = document.getElementById('rpPwC-err');

        // Reset styles
        if (pw) pw.classList.remove('is-invalid');
        if (pwc) pwc.classList.remove('is-invalid');
        if (errPw) errPw.style.display = 'none';
        if (errPwc) errPwc.style.display = 'none';

        // Validation du mot de passe
        if (!pw || !pw.value || pw.value.length < 8) {
            if (pw) pw.classList.add('is-invalid');
            if (errPw) errPw.style.display = 'block';
            ok = false;
        }

        // Validation de la confirmation
        if (pw && pwc && pw.value !== pwc.value) {
            if (pwc) pwc.classList.add('is-invalid');
            if (errPwc) errPwc.style.display = 'block';
            ok = false;
        }

        if (!ok) {
            e.preventDefault();
        } else {
            const btn = document.getElementById('rpBtn');
            const spinner = document.getElementById('rpSpinner');
            const icon = document.getElementById('rpIcon');
            const text = document.getElementById('rpText');

            if (btn) btn.disabled = true;
            if (spinner) spinner.style.display = 'inline-block';
            if (icon) icon.style.display = 'none';
            if (text) text.textContent = 'Enregistrement...';
        }
    });
}
</script>
</body>
</html>
