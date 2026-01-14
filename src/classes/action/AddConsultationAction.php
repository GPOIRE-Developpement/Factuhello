<?php

namespace guillaumepaquin\factuhello\action;

use guillaumepaquin\factuhello\model\AccountModel;
use guillaumepaquin\factuhello\render\ErrorRenderer;
use guillaumepaquin\factuhello\model\PatientModel;

/**
 * Action par défaut : page d'ajout d'une consultation à un patient
 */
class AddConsultationAction extends Action {
    /**
     * Execute lorsqu'il n'y a pas d'action spécifique demandée, retourne la page d'une consultation à un patient
     * @return string
     */
    public static function execute(): string {
        if(!AccountModel::isLoggedIn()){
            header("Location: ?action=login");
            exit();
        }

        $patient = $_POST['patient'] ?? null;
        $date = $_POST['time'] ?? null;
        $benefit = $_POST['benefit'] ?? null;

        if(!isset($patient) || !isset($date) || !isset($benefit)){
            return ErrorRenderer::render("Tous les champs sont obligatoires.", "?action=dashboard");
        }

        return PatientModel::addConsultation($patient, $date, $benefit);
    }
}