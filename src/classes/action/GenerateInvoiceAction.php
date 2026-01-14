<?php

namespace guillaumepaquin\factuhello\action;

use guillaumepaquin\factuhello\model\InvoiceModel;
use guillaumepaquin\factuhello\model\PatientModel;

/**
 * Action pour générer une facture
 */
class GenerateInvoiceAction extends Action {
    public function execute() {
        if (!isset($_POST['patient_id']) || !isset($_POST['consultation_ids'])) {
            return json_encode(['error' => 'Données manquantes']);
        }

        $patientId = $_POST['patient_id'];
        $consultationIds = json_decode($_POST['consultation_ids'], true);
        $reductionPercent = isset($_POST['reduction_percent']) ? floatval($_POST['reduction_percent']) : 0;

        if (empty($consultationIds)) {
            return json_encode(['error' => 'Aucune consultation sélectionnée']);
        }

        // Générer la facture
        return InvoiceModel::generateInvoice($patientId, $consultationIds, $reductionPercent);
    }
}
