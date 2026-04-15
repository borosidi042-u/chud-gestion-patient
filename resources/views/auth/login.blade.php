<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Portail CHUD-BA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .login-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { border: none; border-radius: 15px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.1); width: 100%; max-width: 900px; }
        .login-info { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); color: white; padding: 40px; display: flex; flex-direction: column; justify-content: center; }
        .login-form { background: white; padding: 40px; }
        .btn-primary { padding: 12px; border-radius: 8px; font-weight: 600; background-color: #0d6efd; border: none; }
        .form-control { padding: 12px 15px; border-radius: 8px; border: 1px solid #dee2e6; }
        .form-control:focus { box-shadow: 0 0 0 0.25 margin-bottom: 0.25rem; border-color: #0d6efd; }
        .hospital-logo { font-size: 3rem; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container login-container">
    <div class="card login-card shadow">
        <div class="row g-0">
            <div class="col-md-5 login-info d-none d-md-flex text-center">
                <div class="hospital-logo border border-2 border-white rounded-circle d-inline-block mx-auto px-3 py-2">
                    <i class="bi bi-hospital"></i>
                </div>
                <h2 class="fw-bold mt-3">CHUD Borgou-Alibori</h2>
                <p class="lead">Système de gestion des patients et de facturation</p>
                <div class="mt-auto small opacity-75">
                    &copy; 2026 - Plateforme de Digitalisation Hospitalière
                </div>
            </div>

            <div class="col-md-7 login-form">
                <div class="mb-4">
                    <h3 class="fw-bold text-dark">Bienvenue</h3>
                    <p class="text-muted">Veuillez vous identifier pour accéder au portail</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger border-0 small">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Adresse Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control border-start-0 bg-light" placeholder="nom@exemple.com" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary">Mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control border-start-0 bg-light" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember">
                            <label class="form-check-label small text-muted" for="remember">Se souvenir de moi</label>
                        </div>
                        <a href="#" class="small text-decoration-none">Mot de passe oublié ?</a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3 shadow-sm">
                        Se connecter <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                    <div class="text-center mt-4">
                        <p class="small text-muted">Pas de compte ? <a href="{{ route('register') }}" class="text-decoration-none">S'inscrire</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>