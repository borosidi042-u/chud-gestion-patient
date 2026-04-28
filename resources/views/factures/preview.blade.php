<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aperçu Facture - {{ $facture->numero_facture }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --green: #10B981;
            --green-l: #D1FAE5;
            --blue: #003F6E;
            --blue-l: #DBEAFE;
            --muted: #6B7280;
        }
        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 30px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .invoice-header {
            background: linear-gradient(135deg, #003F6E 0%, #1E40AF 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .invoice-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
        }
        .invoice-header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .invoice-body {
            padding: 30px;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-title {
            font-weight: 600;
            color: #1F2937;
            border-bottom: 2px solid #E5E7EB;
            padding-bottom: 8px;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .info-label {
            color: #6B7280;
            font-size: 0.85rem;
        }
        .info-value {
            font-weight: 500;
            color: #1F2937;
            font-size: 0.95rem;
        }
        .amount-card {
            background: #F9FAFB;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }
        .amount-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #E5E7EB;
        }
        .amount-row:last-child {
            border-bottom: none;
        }
        .amount-total {
            font-weight: 700;
            font-size: 1.2rem;
            color: #003F6E;
        }
        .pec-badge {
            background: #D1FAE5;
            color: #065F46;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            display: inline-block;
        }
        .invoice-footer {
            background: #F9FAFB;
            padding: 20px 30px;
            text-align: center;
            color: #6B7280;
            font-size: 0.8rem;
            border-top: 1px solid #E5E7EB;
        }
        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            cursor: pointer;
            font-weight: 500;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .btn-print {
                display: none;
            }
            .invoice-container {
                box-shadow: none;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">
        <i class="bi bi-printer"></i> Imprimer / Enregistrer PDF
    </button>

    <div class="invoice-container">
        <div class="invoice-header">
            <h1><i class="bi bi-receipt-cutoff"></i> FACTURE</h1>
            <p>N° {{ $facture->numero_facture }}</p>
        </div>

        <div class="invoice-body">
            <div class="info-section">
                <div class="info-title"><i class="bi bi-person"></i> Informations patient</div>
                <div class="info-grid">
                    <div>
                        <div class="info-label">Nom complet</div>
                        <div class="info-value">{{ $facture->patient->prenom }} {{ $facture->patient->nom }}</div>
                    </div>
                    <div>
                        <div class="info-label">Code unique</div>
                        <div class="info-value">{{ $facture->patient->code_unique }}</div>
                    </div>
                    @if($facture->patient->telephone)
                    <div>
                        <div class="info-label">Téléphone</div>
                        <div class="info-value">{{ $facture->patient->telephone }}</div>
                    </div>
                    @endif
                    @if($facture->patient->npi)
                    <div>
                        <div class="info-label">NPI</div>
                        <div class="info-value">{{ $facture->patient->npi }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="info-section">
                <div class="info-title"><i class="bi bi-info-circle"></i> Détails facture</div>
                <div class="info-grid">
                    <div>
                        <div class="info-label">Date de facture</div>
                        <div class="info-value">{{ $facture->date_facture->format('d/m/Y') }}</div>
                    </div>
                    <div>
                        <div class="info-label">Service</div>
                        <div class="info-value">{{ $facture->service->nom_service ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="info-label">Date d'enregistrement</div>
                        <div class="info-value">{{ $facture->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="info-label">Enregistré par</div>
                        <div class="info-value">{{ $facture->user->prenom ?? '' }} {{ $facture->user->nom ?? '' }}</div>
                    </div>
                </div>
            </div>

            <div class="amount-card">
                <div class="amount-row">
                    <span>Montant total de la prestation</span>
                    <span class="fw-bold">{{ number_format($facture->montant, 0, ',', ' ') }} FCFA</span>
                </div>

                @if($facture->has_p_e_c)
                <div class="amount-row">
                    <span>
                        Prise en charge
                        <span class="pec-badge ms-2"><i class="bi bi-shield-fill-check"></i> {{ $facture->pec_organisme }}</span>
                    </span>
                    <span style="color: #10B981;">- {{ number_format($facture->pec_montant, 0, ',', ' ') }} FCFA</span>
                </div>
                @endif

                <div class="amount-row" style="margin-top: 10px; padding-top: 15px; border-top: 2px solid #E5E7EB;">
                    <span class="fw-bold" style="font-size: 1.1rem;">À payer par le patient</span>
                    <span class="amount-total">{{ number_format($facture->montant_patient, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </div>

        <div class="invoice-footer">
            <p><i class="bi bi-check-circle-fill" style="color: #10B981;"></i> Document généré automatiquement par le système</p>
            <p class="mb-0">Merci de votre confiance</p>
        </div>
    </div>
</body>
</html>
