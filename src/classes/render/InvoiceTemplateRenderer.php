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
</head>
<body>
    <h1>FACTURE</h1>
    
    <div>
        <h2>Informations Facture</h2>
        <p><strong>Numéro de facture:</strong> $invoiceId</p>
        <p><strong>Date d'émission:</strong> $createdAt</p>
    </div>

    <div>
        <h2>Informations Patient</h2>
        <p><strong>Nom:</strong> $patientName</p>
        <p><strong>Email:</strong> $patientEmail</p>
        <p><strong>Téléphone:</strong> $patientPhone</p>
        <p><strong>Adresse:</strong> $patientAddress</p>
    </div>

    <div>
        <h2>Détails des Consultations</h2>
        <table border="1">
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

    <div>
        <h2>Récapitulatif</h2>
        <p><strong>Total avant réduction:</strong> $totalBeforeReduction €</p>
        <p><strong>Réduction ($reductionPercent %):</strong> - $reductionAmount €</p>
        <hr>
        <p><strong>TOTAL À PAYER:</strong> $finalAmount €</p>
    </div>

    <div>
        <p>Merci de votre confiance!</p>
        <p>Cette facture a été générée automatiquement.</p>
    </div>
</body>
</html>
HTML;
    }
}
