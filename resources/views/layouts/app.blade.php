<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHUD B/A — @yield('title','Gestion Patients')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--blue:#005A9C;--blue-d:#003F6E;--blue-l:#E8F3FC;--green:#00875A;--green-l:#E3F5EF;
              --red:#C8001A;--red-l:#FDECEA;--amber:#D97706;--amber-l:#FFFBEB;
              --bg:#EEF3F9;--text:#1A2942;--muted:#6B7A8D;--sw:260px;
              --r:12px;--sh:0 2px 12px rgba(0,90,156,.08);--sh2:0 6px 28px rgba(0,90,156,.14)}
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);line-height:1.6}
        .sidebar{position:fixed;top:0;left:0;width:var(--sw);height:100vh;background:var(--blue-d);
            display:flex;flex-direction:column;z-index:200;overflow-y:auto;transition:transform .3s;
            box-shadow:4px 0 24px rgba(0,0,0,.15)}
        .s-logo{padding:22px 45px 16px;border-bottom:1px solid rgba(255,255,255,.1);text-align:center}

        /* Style modifié pour le logo image */
        .s-logo-img-container{width:100%;height: 60px;padding:10px;background:#E8F3FC;border-radius:11px;
            display:flex;align-items:center;justify-content:center;margin-bottom:12px;overflow:hidden}
        .s-logo-img{width:300%;height:95px;object-fit:contain ;}

        .s-logo-name{color:#fff;font-weight:700;font-size:1rem}
        .s-logo-sub{color:rgba(255,255,255,.45);font-size:.68rem;margin-top:2px;line-height:1.4}
        .s-section{padding:16px 16px 4px;font-size:.62rem;font-weight:700;text-transform:uppercase;
            letter-spacing:1.8px;color:rgba(255,255,255,.3)}
        .s-nav{flex:1;padding:6px 10px}
        .s-link{display:flex;align-items:center;gap:10px;padding:10px 13px;border-radius:9px;
            color:rgba(255,255,255,.68);font-size:.855rem;font-weight:500;text-decoration:none;
            margin-bottom:2px;transition:all .2s}
        .s-link:hover{background:rgba(255,255,255,.1);color:#fff}
        .s-link.active{background:var(--blue);color:#fff;box-shadow:0 2px 10px rgba(0,90,156,.45)}
        .s-link i{font-size:1rem;width:20px;text-align:center;flex-shrink:0}
        .s-foot{padding:14px 16px;border-top:1px solid rgba(255,255,255,.1)}
        .u-card{background:rgba(255,255,255,.08);border-radius:9px;padding:10px 12px;margin-bottom:10px}
        .u-name{color:#fff;font-size:.82rem;font-weight:600}
        .u-role{display:inline-block;border-radius:20px;padding:2px 9px;font-size:.67rem;margin-top:3px}
        .u-role.admin{background:var(--red);color:#fff}
        .u-role.agent{background:rgba(255,255,255,.18);color:rgba(255,255,255,.85)}
        .btn-logout{width:100%;padding:8px;border-radius:8px;background:transparent;
            border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.68);
            font-size:.82rem;cursor:pointer;transition:all .2s;font-family:inherit}
        .btn-logout:hover{background:rgba(255,255,255,.1);color:#fff}
        .mob-toggle{display:none;position:fixed;top:14px;left:14px;z-index:300;
            background:var(--blue-d);color:#fff;border:none;border-radius:9px;
            width:40px;height:40px;font-size:1.1rem;cursor:pointer;box-shadow:var(--sh2);
            align-items:center;justify-content:center}
        .s-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:150}
        .main{margin-left:var(--sw);min-height:100vh;padding:24px;transition:margin-left .3s}
        .topbar{background:#fff;border-radius:var(--r);padding:14px 20px;margin-bottom:22px;
            display:flex;justify-content:space-between;align-items:center;box-shadow:var(--sh);animation:fadeDown .4s}
        .topbar-title{font-weight:700;font-size:1.08rem;color:var(--text)}
        .topbar-date{font-size:.78rem;color:var(--muted)}
        .card{background:#fff;border:none;border-radius:var(--r);box-shadow:var(--sh);animation:fadeUp .4s both}
        .card-header{background:#fff;font-weight:600;font-size:.88rem;border-bottom:1px solid #EEF2F7;
            padding:14px 18px;border-radius:var(--r) var(--r) 0 0 !important}
        .card-footer{background:#fff;border-top:1px solid #EEF2F7;border-radius:0 0 var(--r) var(--r) !important}
        .alert{border:none;border-radius:10px;font-size:.875rem;animation:fadeDown .4s}
        .alert-success{background:var(--green-l);color:#00533A}
        .alert-danger{background:var(--red-l);color:#7A000F}
        .alert-info{background:var(--blue-l);color:var(--blue-d)}
        .alert-warning{background:var(--amber-l);color:#7A4300}
        .alert-secondary{background:#F0F4F8;color:var(--muted)}
        .btn{border-radius:8px;font-weight:500;font-size:.855rem;transition:all .2s;font-family:inherit}
        .btn-primary{background:var(--blue);border-color:var(--blue)}
        .btn-primary:hover{background:var(--blue-d);border-color:var(--blue-d);transform:translateY(-1px);box-shadow:0 4px 14px rgba(0,90,156,.3)}
        .btn-success{background:var(--green);border-color:var(--green)}
        .btn-success:hover{background:#006644;border-color:#006644;transform:translateY(-1px)}
        .btn-danger{background:var(--red);border-color:var(--red)}
        .btn-warning{background:var(--amber);border-color:var(--amber);color:#fff}
        .btn-warning:hover{background:#B45309;border-color:#B45309;color:#fff}
        .btn-outline-primary{color:var(--blue);border-color:var(--blue)}
        .btn-outline-primary:hover{background:var(--blue);border-color:var(--blue)}
        .btn-outline-secondary{color:var(--muted);border-color:#DDE3EC}
        .btn-outline-secondary:hover{background:#F0F4F8;color:var(--text);border-color:#DDE3EC}
        .btn-outline-danger{color:var(--red);border-color:var(--red)}
        .btn-outline-danger:hover{background:var(--red);color:#fff}
        .form-control,.form-select{border-radius:8px;border:1.5px solid #DDE3EC;
            font-size:.875rem;padding:9px 14px;transition:all .2s;font-family:inherit;color:var(--text)}
        .form-control:focus,.form-select:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(0,90,156,.12)}
        .form-control.is-invalid,.form-select.is-invalid{border-color:var(--red)!important}
        .form-control.is-valid{border-color:var(--green)!important}
        .form-label{font-weight:600;font-size:.8rem;color:var(--text);margin-bottom:5px}
        .invalid-feedback{font-size:.77rem;color:var(--red)}
        .valid-feedback{font-size:.77rem;color:var(--green)}
        .field-hint{font-size:.75rem;color:var(--muted);margin-top:3px}
        .table thead th{background:#F5F8FC;font-size:.72rem;text-transform:uppercase;letter-spacing:.8px;
            color:var(--muted);border-bottom:2px solid #EEF2F7;padding:11px 16px;font-weight:700}
        .table td{padding:11px 16px;vertical-align:middle;font-size:.87rem;border-color:#F0F4F8}
        .table-hover tbody tr{transition:background .15s}
        .table-hover tbody tr:hover{background:var(--blue-l)}
        .code-badge{font-family:'Courier New',monospace;background:#EEF2F7;color:var(--blue-d);
            padding:3px 9px;border-radius:6px;font-size:.79rem;font-weight:700;letter-spacing:.5px}
        .stat-card{border-radius:var(--r);padding:20px 22px;background:#fff;box-shadow:var(--sh);
            display:flex;align-items:center;gap:16px;transition:transform .25s,box-shadow .25s;animation:fadeUp .4s both}
        .stat-card:hover{transform:translateY(-4px);box-shadow:var(--sh2)}
        .stat-icon{width:54px;height:54px;border-radius:14px;display:flex;align-items:center;
            justify-content:center;font-size:1.5rem;flex-shrink:0}
        .stat-num{font-size:1.9rem;font-weight:700;line-height:1.1}
        .stat-lbl{font-size:.76rem;color:var(--muted);margin-top:3px}
        .hist-item{border-radius:10px;padding:12px 14px;margin-bottom:10px;background:#F8FAFD;transition:box-shadow .2s}
        .hist-item:hover{box-shadow:var(--sh)}
        .hist-circuit{border-left:3px solid var(--blue)}
        .hist-facture{border-left:3px solid var(--green)}
        /* Dropdown patient */
        .patient-dropdown{border:1.5px solid #DDE3EC;border-top:none;border-radius:0 0 8px 8px;
            max-height:220px;overflow-y:auto;background:#fff;box-shadow:0 6px 20px rgba(0,90,156,.12);
            position:absolute;left:0;right:0;z-index:99}
        .patient-dropdown-item{padding:10px 14px;cursor:pointer;border-bottom:1px solid #F0F4F8;
            font-size:.875rem;transition:background .15s}
        .patient-dropdown-item:hover{background:var(--blue-l)}
        .patient-dropdown-item:last-child{border-bottom:none}
        @keyframes fadeDown{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}
        @keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
        .card:nth-child(2){animation-delay:.05s}.card:nth-child(3){animation-delay:.1s}
        .stat-card:nth-child(1){animation-delay:.05s}.stat-card:nth-child(2){animation-delay:.1s}
        .stat-card:nth-child(3){animation-delay:.15s}.stat-card:nth-child(4){animation-delay:.2s}
        @media(max-width:991px){
            .sidebar{transform:translateX(-100%)}.sidebar.open{transform:translateX(0)}
            .s-overlay.open{display:block}.mob-toggle{display:flex}
            .main{margin-left:0;padding:14px;padding-top:62px}}
        @media(max-width:576px){
            .topbar{flex-direction:column;align-items:flex-start;gap:4px}
            .stat-card{padding:14px 16px}.stat-num{font-size:1.5rem}
            .table thead th,.table td{padding:9px 10px;font-size:.8rem}}
    </style>
</head>
<body>
<button class="mob-toggle" id="mToggle"><i class="bi bi-list"></i></button>
<div class="s-overlay" id="sOverlay"></div>
<aside class="sidebar" id="sidebar">
    <div class="s-logo">
        <div class="s-logo-img-container">
            <img src="{{ asset('images/image.png') }}" alt="Logo CHUD-BA" class="s-logo-img">
        </div>
        <div class="s-logo-name">CHUD B/A</div>
        <div class="s-logo-sub">Gestion Patients &amp; Factures<br>Borgou-Alibori</div>
    </div>
    <nav class="s-nav">
        <a href="{{ route('dashboard') }}" class="s-link {{ request()->routeIs('dashboard')?'active':'' }}">
            <i class="bi bi-speedometer2"></i> Tableau de bord
        </a>
        <div class="s-section">Patients</div>
        <a href="{{ route('patients.index') }}" class="s-link {{ request()->routeIs('patients.index','patients.show','patients.edit')?'active':'' }}">
            <i class="bi bi-people-fill"></i> Liste des patients
        </a>
        <div class="s-section">Activités</div>
        <a href="{{ route('factures.index') }}" class="s-link {{ request()->routeIs('factures.*')?'active':'' }}">
            <i class="bi bi-receipt-cutoff"></i> Factures
        </a>
        <a href="{{ route('circuits.create') }}" class="s-link {{ request()->routeIs('circuits.*')?'active':'' }}">
            <i class="bi bi-diagram-3-fill"></i> Circuit patient
        </a>
        @if(Auth::user()->role==='admin')
        <div class="s-section">Administration</div>
        <a href="{{ route('admin.services.index') }}" class="s-link {{ request()->routeIs('admin.services.*')?'active':'' }}">
            <i class="bi bi-building-fill"></i> Services
        </a>
        <a href="{{ route('admin.users.index') }}" class="s-link {{ request()->routeIs('admin.users.*')?'active':'' }}">
            <i class="bi bi-person-gear"></i> Utilisateurs
        </a>
        @endif
    </nav>
    

    <div class="s-foot">
        <div class="u-card">
            <div class="u-name"><i class="bi bi-person-circle me-1"></i>{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</div>
            <span class="u-role {{ Auth::user()->role==='admin'?'admin':'agent' }}">
                {{ Auth::user()->role==='admin'?'Administrateur':"Agent d'accueil" }}
            </span>
            <div style="margin-top:8px">
                <a href="{{ route('profile.index') }}"
                style="font-size:.75rem;color:rgba(255,255,255,.65);text-decoration:none;display:flex;align-items:center;gap:5px">
                    <i class="bi bi-gear-fill"></i> Mon profil
                </a>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout"><i class="bi bi-box-arrow-left me-1"></i>Déconnexion</button>
        </form>
    </div>
</aside>
<main class="main">
    <div class="topbar">
        <div class="topbar-title"><i class="bi bi-hospital me-2" style="color:var(--blue)"></i>@yield('title','Tableau de bord')</div>
        <div class="topbar-date"><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::now()->locale('fr_FR')->isoFormat('dddd D MMMM YYYY') }}</div>
    </div>
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @yield('content')
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const tog=document.getElementById('mToggle'),sb=document.getElementById('sidebar'),ov=document.getElementById('sOverlay');
tog.addEventListener('click',()=>{sb.classList.toggle('open');ov.classList.toggle('open')});
ov.addEventListener('click',()=>{sb.classList.remove('open');ov.classList.remove('open')});
</script>
@yield('scripts')
</body>
</html>y
