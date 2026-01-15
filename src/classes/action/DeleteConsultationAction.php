<?php

namespace guillaumepaquin\factuhello\action;

use guillaumepaquin\factuhello\model\PatientModel;

/**
 * Action de suppression d'une consultation
 */
class DeleteConsultationAction extends Action {
    /**
     * Exécute la suppression d'une consultation
     * @return string HTML de réponse
     */
    public static function execute(): string {
        $consultationId = $_GET['id'] ?? null;
        $patientId = $_GET['patient'] ?? null;
        
        return PatientModel::deleteConsultation($consultationId, $patientId);
    }
}
