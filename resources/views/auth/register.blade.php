<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Portail CHUD-BA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .register-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .register-card { border: none; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); width: 100%; max-width: 600px; background: white; }
        .btn-primary { padding: 12px; border-radius: 8px; font-weight: 600; background-color: #0d6efd; }
        .form-control { padding: 12px; border-radius: 8px; background-color: #f8f9fa; }
        .header-box { background: #0d6efd; color: white; padding: 30px; border-radius: 15px 15px 0 0; }
    </style>
</head>
<body>

<div class="container register-container">
    <div class="card register-card">
        <div class="header-box text-center">
            <i class="bi bi-person-plus fs-1"></i>
            <h3 class="fw-bold mt-2">Créer un compte Agent</h3>
            <p class="mb-0 opacity-75">Centre Hospitalier Universitaire Départemental</p>
        </div>
        
        <div class="card-body p-4 p-md-5">
            {{-- Affichage des erreurs si la validation échoue --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Nom</label>
                        <input type="text" name="nom" class="form-control" placeholder="Ex: DOSSOUN" value="{{ old('nom') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Prénom</label>
                        <input type="text" name="prenom" class="form-control" placeholder="Ex: Jean" value="{{ old('prenom') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Adresse Email</label>
                    <input type="email" name="email" class="form-control" placeholder="agent@chud.bj" value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Mot de passe</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 shadow-sm">
                    Finaliser l'inscription <i class="bi bi-check-circle ms-2"></i>
                </button>

                <div class="text-center mt-4">
                    <p class="small text-muted">Déjà un compte ? <a href="{{ route('login') }}" class="text-decoration-none">Se connecter</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>