<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHUD-BA - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; }
        
        /* Fixer la barre latérale */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            padding: 20px;
            background-color: #0d6efd; /* Le bleu de ta capture */
            color: white;
            overflow-y: auto;
        }

        /* Décaler le contenu principal vers la droite */
        main {
            margin-left: 250px; /* Largeur de la sidebar */
            padding: 20px;
            width: calc(100% - 250px);
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            margin-bottom: 5px;
            border-radius: 8px;
            padding: 10px 15px;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.15);
        }

        .sidebar hr { border-top: 1px solid rgba(255,255,255,0.2); }

        .card-stat { border: none; border-left: 5px solid #0d6efd; border-radius: 10px; }
        
        /* Responsive : sur petit écran la sidebar redevient normale */
        @media (max-width: 768px) {
            .sidebar { position: relative; min-height: auto; width: 100%; }
            main { margin-left: 0; width: 100%; }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar">
            <div class="text-center mb-4">
                <i class="bi bi-hospital fs-1"></i>
                <h5 class="mt-2 fw-bold">CHUD-BA</h5>
                <div class="small opacity-75 mt-2">
                    {{ Auth::user()->nom }} {{ Auth::user()->prenom }}<br>
                    <span class="badge bg-light text-primary mt-1">{{ ucfirst(Auth::user()->role) }}</span>
                </div>
                <hr>
            </div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i> Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="bi bi-people me-2"></i> Patients</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="bi bi-file-earmark-medical me-2"></i> Factures</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="bi bi-diagram-3 me-2"></i> Circuit Patient</a>
                </li>

                {{-- ADMINISTRATION : Apparaît seulement pour l'admin --}}
                @if(Auth::user()->role === 'administrateur')
                <li class="nav-item mt-4">
                    <p class="ps-3 small text-uppercase fw-bold" style="color: rgba(255,255,255,0.5); letter-spacing: 1px;">Admin</p>
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="bi bi-shield-lock me-2"></i> Agents & Rôles
                    </a>
                </li>
                @endif

                <li class="nav-item mt-auto pt-4">
                    <hr>
                    <form action="{{ route('logout') }}" method="POST" class="px-2">
                        @csrf
                        <button type="submit" class="btn btn-link text-white text-decoration-none w-100 text-start nav-link">
                            <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        <main>
            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>