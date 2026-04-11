<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - CHUD BA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { overflow-x: hidden; }
        /* Style de la barre verticale */
        .sidebar {
            min-height: 100vh;
            background-color: #0d6efd; /* Bleu Primary */
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        .card-stat { border: none; border-left: 5px solid #0d6efd; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-3">
            <div class="text-center mb-4">
                <i class="bi bi-hospital fs-1"></i>
                <h5 class="mt-2">CHUD-BA</h5>
                <hr>
            </div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i> Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-people me-2"></i> Patients
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-file-earmark-medical me-2"></i> Factures
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-diagram-3 me-2"></i> Circuit Patient
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <hr>
                    <a class="nav-link text-danger" href="#">
                        <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                    </a>
                </li>
            </ul>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Résumé de l'activité</h1>
                <div class="text-muted">{{ now()->format('d/m/Y') }}</div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card card-stat shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 bg-primary-subtle p-3 rounded">
                                <i class="bi bi-people text-primary fs-3"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-0">Total Patients</h6>
                                <span class="h4 fw-bold">{{ $stats['patients_count'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-stat shadow-sm p-3" style="border-left-color: #198754;">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 bg-success-subtle p-3 rounded">
                                <i class="bi bi-receipt text-success fs-3"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-0">Factures (Aujourd'hui)</h6>
                                <span class="h4 fw-bold">{{ $stats['factures_today'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 bg-white p-4">
                <h5>Actions rapides</h5>
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary">Enregistrer un nouveau patient</button>
                    <button class="btn btn-outline-success">Saisir une facture</button>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>