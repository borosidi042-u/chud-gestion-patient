<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $facture->numero_facture }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Arial, sans-serif;
            background: white;
            padding: 40px;
            font-size: 12px;
        }
        .invoice-box {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }
        .header {
            background: #2563EB;
            color: white;
            padding: 25px;
            text-align: center;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 25px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1F2937;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 5px;
            margin-bottom: 12px;
        }
        .row {
            display: flex;
            margin-bottom: 8px;
        }
        .label {
            width: 140px;
            color: #6B7280;
        }
        .value {
            flex: 1;
            font-weight: 500;
        }
        .amount-table {
            margin-top: 20px;
            background: #F9FAFB;
            padding: 15px;
            border-radius: 6px;
        }
        .amount-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .amount-row:last-child {
            border-bottom: none;
        }
        .total {
            font-size: 16px;
            font-weight: bold;
            color: #2563EB;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #2563EB;
        }
        .pec-badge {
            background: #D1FAE5;
            color: #065F46;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            display: inline-block;
        }
        .footer {
            background: #F9FAFB;
            padding: 15px;
            text-align: center;
            font-size: 10px;
            color: #6B7280;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <h1>FACTURE</h1>
            <p>N° {{ $facture->numero_facture }}</p>
        </div>

        <div class="content">
            <div class="section">
                <div class="section-title">Informations patient</div>
                <div class="row">
                    <div class="label">Nom complet :</div>
                    <div class="value">{{ $facture->patient->prenom }} {{ $facture->patient->nom }}</div>
                </div>
                <div class="row">
                    <div class="label">Code unique :</div>
                    <div class="value">{{ $facture->patient->code_unique }}</div>
                </div>
                @if($facture->patient->telephone)
                <div class="row">
                    <div class="label">Téléphone :</div>
                    <div class="value">{{ $facture->patient->telephone }}</div>
                </div>
                @endif
            </div>

            <div class="section">
                <div class="section-title">Détails facture</div>
                <div class="row">
                    <div class="label">Date de facture :</div>
                    <div class="value">{{ $facture->date_facture->format('d/m/Y') }}</div>
                </div>
                <div class="row">
                    <div class="label">Service :</div>
                    <div class="value">{{ $facture->service->nom_service ?? '—' }}</div>
                </div>
                <div class="row">
                    <div class="label">Enregistré par :</div>
                    <div class="value">{{ $facture->user->prenom ?? '' }} {{ $facture->user->nom ?? '' }}</div>
                </div>
            </div>

            <div class="amount-table">
                <div class="amount-row">
                    <span>Montant total</span>
                    <span>{{ number_format($facture->montant, 0, ',', ' ') }} FCFA</span>
                </div>

                @if($facture->has_p_e_c)
                <div class="amount-row">
                    <span>
                        Prise en charge ({{ $facture->pec_organisme }})
                    </span>
                    <span style="color: #10B981;">- {{ number_format($facture->pec_montant, 0, ',', ' ') }} FCFA</span>
                </div>
                @endif

                <div class="amount-row total">
                    <span>À payer par le patient</span>
                    <span>{{ number_format($facture->montant_patient, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </div>

        <div class="footer">
            Document généré automatiquement le {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
