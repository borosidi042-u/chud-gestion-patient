<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body{font-family:'Segoe UI',Arial,sans-serif;background:#EEF3F9;margin:0;padding:20px}
        .wrap{max-width:500px;margin:0 auto}
        .card{background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.1)}
        .header{background:linear-gradient(135deg,#003F6E,#005A9C);padding:32px;text-align:center}
        .header h1{color:#fff;font-size:1.2rem;margin:0;font-weight:700}
        .header p{color:rgba(255,255,255,.7);font-size:.82rem;margin:6px 0 0}
        .body{padding:32px}
        .intro{color:#1A2942;font-size:.95rem;line-height:1.6;margin-bottom:24px}
        .code-box{background:#E8F3FC;border:2px dashed #005A9C;border-radius:12px;
            padding:20px;text-align:center;margin:24px 0}
        .code{font-size:2.8rem;font-weight:700;color:#003F6E;letter-spacing:12px;
            font-family:'Courier New',monospace}
        .code-label{font-size:.78rem;color:#6B7A8D;margin-top:8px}
        .warning{background:#FFFBEB;border-left:3px solid #D97706;border-radius:6px;
            padding:12px 16px;font-size:.82rem;color:#7A4300;margin-top:20px}
        .footer{background:#F5F8FC;padding:20px;text-align:center;
            font-size:.75rem;color:#6B7A8D;border-top:1px solid #EEF2F7}
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="header">
            <h1>🏥 CHUD Borgou-Alibori</h1>
            <p>Système de Gestion des Patients</p>
        </div>
        <div class="body">
            <div class="intro">
                Vous avez demandé à réinitialiser votre mot de passe.<br>
                Voici votre <strong>code de vérification à 4 chiffres</strong> :
            </div>
            <div class="code-box">
                <div class="code">{{ $code }}</div>
                <div class="code-label">Ce code expire dans <strong>10 minutes</strong></div>
            </div>
            <div class="warning">
                ⚠️ Si vous n'avez pas demandé cette réinitialisation, ignorez cet email. Votre mot de passe ne sera pas modifié.
            </div>
        </div>
        <div class="footer">
            CHUD Borgou-Alibori — Système de gestion patients &amp; factures<br>
            Cet email a été envoyé automatiquement, ne pas répondre.
        </div>
    </div>
</div>
</body>
</html>