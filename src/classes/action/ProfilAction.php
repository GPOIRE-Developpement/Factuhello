<?php

namespace guillaumepaquin\factuhello\action;

use guillaumepaquin\factuhello\model\AccountModel;
use guillaumepaquin\factuhello\render\ProfilRenderer;
use guillaumepaquin\factuhello\model\PatientModel;
use guillaumepaquin\factuhello\render\ErrorRenderer;

/**
 * Action pour afficher le profil d'un patient
 */
class ProfilAction extends Action {
    /**
     * Execute l'action d'affichage du profil
     * @return string
     */
    public static function execute(): string {
        if(!AccountModel::isLoggedIn()){
            header("Location: ?action=login");
            exit();
        }

        $id = $_GET['id'] ?? null;

        $patient = PatientModel::getPatientById($id);

        if(!$patient){
            return ErrorRenderer::render("Patient non trouvé.", "?action=dashboard");
        }

        return ProfilRenderer::render($patient['id'], $patient['email'], $patient['name'], $patient['phone'], $patient['address'], $patient['nb_consultations'], $patient['nb_invoices']);    
    }
}
