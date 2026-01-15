<?php

namespace guillaumepaquin\factuhello\action;

use guillaumepaquin\factuhello\action\Action;
use guillaumepaquin\factuhello\model\InvoiceModel;
use guillaumepaquin\factuhello\render\ErrorRenderer;

/**
 * Action pour générer une facture
 */
class GenerateInvoiceAction extends Action {
    public static function execute(): string {
        if (!isset($_POST['patient_id']) || !isset($_POST['consultation_ids'])) {
            return ErrorRenderer::render("Erreur : paramètres manquants pour la génération de la facture.","?action=dashboard");
        }

        $patientId = $_POST['patient_id'];
        $consultationIds = json_decode($_POST['consultation_ids'], true);
        $reductionPercent = isset($_POST['reduction_percent']) ? floatval($_POST['reduction_percent']) : 0;

        var_dump($consultationIds);

        // Générer la facture
        return InvoiceModel::generateInvoice($patientId, $consultationIds, $reductionPercent);
    }
}
