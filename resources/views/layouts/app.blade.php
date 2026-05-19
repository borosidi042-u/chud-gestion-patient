<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CHUD B/A - @yield('title', 'Gestion des patients')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        :root {
            --blue: #005A9C;
            --blue-d: #003F6E;
            --blue-l: #E8F3FC;
            --green: #00875A;
            --green-l: #E3F5EF;
            --red: #C8001A;
            --red-l: #FDECEA;
            --amber: #D97706;
            --amber-l: #FFFBEB;
            --text: #1A2942;
            --muted: #6B7A8D;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #F5F7FC;
            color: var(--text);
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: linear-gradient(180deg, var(--blue-d) 0%, var(--blue) 100%);
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            animation: fadeInDown 0.5s ease;
        }

        .sidebar-logo {
            width: 70px;
            height: 70px;
            background: #fff;
            border-radius: 16px;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(0,0,0,.15);
            animation: pulse 2s infinite;
        }

        .sidebar-logo img {
            width: 90%;
            height: 90%;
            object-fit: contain;
        }

        .sidebar-logo-fallback {
            font-size: 2rem;
            color: var(--blue);
        }

        .sidebar-header h3 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
            font-weight: 600;
        }

        .sidebar-header p {
            font-size: 0.7rem;
            opacity: 0.7;
            margin-bottom: 0;
        }

        /* Profil utilisateur dans le menu */
        .user-profile-card {
            margin: 15px;
            padding: 12px;
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideInLeft 0.5s ease;
        }

        .user-avatar-menu {
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: bold;
        }

        .user-info-menu {
            flex: 1;
        }

        .user-name-menu {
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 2px;
        }

        .user-role-menu {
            font-size: 0.7rem;
            opacity: 0.7;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.85);
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideInLeft 0.5s ease;
            animation-fill-mode: backwards;
        }

        .sidebar .nav-link:nth-child(1) { animation-delay: 0.05s; }
        .sidebar .nav-link:nth-child(2) { animation-delay: 0.1s; }
        .sidebar .nav-link:nth-child(3) { animation-delay: 0.15s; }
        .sidebar .nav-link:nth-child(4) { animation-delay: 0.2s; }
        .sidebar .nav-link:nth-child(5) { animation-delay: 0.25s; }
        .sidebar .nav-link:nth-child(6) { animation-delay: 0.3s; }
        .sidebar .nav-link:nth-child(7) { animation-delay: 0.35s; }
        .sidebar .nav-link:nth-child(8) { animation-delay: 0.4s; }
        .sidebar .nav-link:nth-child(9) { animation-delay: 0.45s; }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            border-left: 3px solid white;
            color: white;
        }

        .sidebar .nav-link i {
            width: 22px;
            font-size: 1.1rem;
        }

        /* Main content */
        .main-content {
            margin-left: 260px;
            padding: 20px;
            min-height: 100vh;
            animation: fadeIn 0.5s ease;
        }

        /* Cards statistiques */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-num {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-lbl {
            font-size: 0.75rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Code badge */
        .code-badge {
            font-family: 'Courier New', monospace;
            background: var(--blue-l);
            color: var(--blue);
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Field hint */
        .field-hint {
            font-size: 0.7rem;
            color: var(--muted);
            margin-top: 4px;
        }

        /* Patient dropdown */
        .patient-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #DDE3EC;
            border-radius: 8px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .patient-dropdown-item {
            padding: 8px 12px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .patient-dropdown-item:hover {
            background: var(--blue-l);
        }

        /* Animations globales */
        .animate__animated {
            animation-duration: 0.6s;
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeInUp 0.5s ease backwards;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .shake {
            animation: shake 0.5s ease;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .menu-toggle {
                display: block;
                position: fixed;
                top: 15px;
                left: 15px;
                z-index: 1001;
                background: var(--blue);
                color: white;
                border: none;
                border-radius: 8px;
                padding: 8px 12px;
            }
        }

        @media (min-width: 769px) {
            .menu-toggle {
                display: none;
            }
        }
    </style>
</head>
<body>

<button class="menu-toggle" id="menuToggle">
    <i class="bi bi-list fs-4"></i>
</button>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            @if(file_exists(public_path('images/image.png')))
                <img src="{{ asset('images/image.png') }}" alt="Logo CHUD-BA">
            @else
                <i class="bi bi-hospital-fill sidebar-logo-fallback"></i>
            @endif
        </div>
        <h3>CHUD Borgou-Alibori</h3>
        <p>Gestion des patients</p>
    </div>

    {{-- Profil utilisateur dans le menu --}}
    <div class="user-profile-card">
        <div class="user-avatar-menu">
            {{ strtoupper(substr(Auth::user()->prenom,0,1).substr(Auth::user()->nom,0,1)) }}
        </div>
        <div class="user-info-menu">
            <div class="user-name-menu">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</div>
             <div class="user-role-menu">
                @if(Auth::user()->role === 'admin')
                    <i class="bi bi-shield-fill-check me-1"></i>Administrateur
                @elseif(Auth::user()->role === 'infirmier')
                    <i class="bi bi-hospital me-1"></i>Infirmier
                @else
                    <i class="bi bi-person-badge me-1"></i>Agent d'accueil
                @endif
            </div>

        </div>
    </div>

    <ul class="nav flex-column mt-2">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2"></i> Tableau de bord
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}" href="{{ route('patients.index') }}">
                <i class="bi bi-people-fill"></i> Patients
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('circuits.*') ? 'active' : '' }}" href="{{ route('circuits.create') }}">
                <i class="bi bi-diagram-3-fill"></i> Nouveau mouvement
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('lits.*') ? 'active' : '' }}" href="{{ route('lits.index') }}">
                <i class="bi bi-hospital-fill"></i> État des lits
            </a>
        </li>

        @if(Auth::user()->role === 'admin')
        <li class="nav-item mt-3">
            <hr style="border-color: rgba(255,255,255,0.1); margin: 0 1.5rem;">
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                <i class="bi bi-person-gear"></i> Utilisateurs
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}" href="{{ route('admin.services.index') }}">
                <i class="bi bi-building"></i> Services
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.salles.*') ? 'active' : '' }}" href="{{ route('admin.salles.index') }}">
                <i class="bi bi-door-open"></i> Salles
            </a>
        </li>
        @endif

        <li class="nav-item mt-3">
            <hr style="border-color: rgba(255,255,255,0.1); margin: 0 1.5rem;">
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.index') }}">
                <i class="bi bi-person-circle"></i> Mon profil
            </a>
        </li>
        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <button type="submit" class="nav-link" style="background:none; border:none; width:100%; text-align:left; cursor:pointer;">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </button>
            </form>
        </li>
    </ul>
</div>

<div class="main-content">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @yield('content')
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Toggle menu mobile
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');

    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }

    // Fermer le menu en cliquant à l'extérieur sur mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                sidebar.classList.remove('show');
            }
        }
    });

    // Animation de shake pour les erreurs de formulaire
    document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.add('shake');
        setTimeout(() => el.classList.remove('shake'), 500);
    });
</script>

@yield('scripts')

</body>
</html>
