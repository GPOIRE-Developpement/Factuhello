<?php

namespace guillaumepaquin\factuhello\render;

/**
 * Classe responsable du rendu du template de facture PDF
 */
class InvoiceTemplateRenderer {
    /**
     * Génère le HTML de la facture pour conversion en PDF
     * @param int $invoiceId Numéro de facture
     * @param string $createdAt Date d'émission
     * @param string $patientName Nom du patient
     * @param string $patientEmail Email du patient
     * @param string $patientPhone Téléphone du patient
     * @param string $patientAddress Adresse du patient
     * @param string $consultationsHTML Lignes HTML des consultations
     * @param string $totalBeforeReduction Total avant réduction formaté
     * @param string $reductionPercent Pourcentage de réduction formaté
     * @param string $reductionAmount Montant de réduction formaté
     * @param string $finalAmount Total final formaté
     * @return string HTML complet de la facture
     */
    public static function render(
        $invoiceId,
        $createdAt,
        $patientName,
        $patientEmail,
        $patientPhone,
        $patientAddress,
        $consultationsHTML,
        $totalBeforeReduction,
        $reductionPercent,
        $reductionAmount,
        $finalAmount
    ): string {
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #$invoiceId</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #1b2430;
            background: #f5f7fb;
            padding: 40px;
            line-height: 1.5;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.10);
            padding: 40px;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #1a73e8;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: 700;
            color: #1a73e8;
            letter-spacing: 2px;
        }
        .invoice-meta {
            text-align: right;
        }
        .invoice-meta p {
            margin: 4px 0;
            color: #5b6575;
        }
        .invoice-meta strong {
            color: #1b2430;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: #1a73e8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #d0d7e2;
        }
        .patient-info {
            background: #e9f0ff;
            border-radius: 12px;
            padding: 20px;
        }
        .patient-info p {
            margin: 6px 0;
            color: #5b6575;
        }
        .patient-info strong {
            color: #1b2430;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #1a73e8;
            color: #ffffff;
            padding: 12px 16px;
            text-align: left;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        th:first-child {
            border-radius: 8px 0 0 8px;
        }
        th:last-child {
            border-radius: 0 8px 8px 0;
            text-align: right;
        }
        td {
            padding: 14px 16px;
            border-bottom: 1px solid #d0d7e2;
            color: #1b2430;
        }
        td:last-child {
            text-align: right;
            font-weight: 500;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .totals {
            background: #f5f7fb;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            color: #5b6575;
        }
        .totals-row.final {
            border-top: 2px solid #1a73e8;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 18px;
            font-weight: 700;
            color: #1a73e8;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #d0d7e2;
            color: #5b6575;
            font-size: 13px;
        }
        .footer p {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="invoice-title">FACTURE</div>
            <div class="invoice-meta">
                <p><strong>N° $invoiceId</strong></p>
                <p>Date : $createdAt</p>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Patient</div>
            <div class="patient-info">
                <p><strong>$patientName</strong></p>
                <p>$patientEmail</p>
                <p>$patientPhone</p>
                <p>$patientAddress</p>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Détails des consultations</div>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type de consultation</th>
                        <th>Prix</th>
                    </tr>
                </thead>
                <tbody>
                    $consultationsHTML
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Récapitulatif</div>
            <div class="totals">
                <div class="totals-row">
                    <span>Total avant réduction</span>
                    <span>$totalBeforeReduction €</span>
                </div>
                <div class="totals-row">
                    <span>Réduction ($reductionPercent %)</span>
                    <span>- $reductionAmount €</span>
                </div>
                <div class="totals-row final">
                    <span>Total à payer</span>
                    <span>$finalAmount €</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Merci de votre confiance</p>
            <p>Facture générée automatiquement par Factuhello</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
