<?php

namespace guillaumepaquin\factuhello\action;

use guillaumepaquin\factuhello\model\AccountModel;
use guillaumepaquin\factuhello\render\ErrorRenderer;
use guillaumepaquin\factuhello\model\PatientModel;

/**
 * Action par défaut : page de suppression d'un patient
 */
class RemovePatientAction extends Action {
    /**
     * Execute lorsqu'il n'y a pas d'action spécifique demandée, retourne la page de suppression d'un patient
     * @return string
     */
    public static function execute(): string {
        if(!AccountModel::isLoggedIn()){
            header("Location: ?action=login");
            exit();
        }

        $id = $_POST['id'];

        if(!isset($id)){
            $url = "?action=dashboard";
            if(isset($id)){
                $url = "?action=profil&id=" . urlencode($id);
            }

            return ErrorRenderer::render("L'identifiant de l'utilisateur à supprimer est manquant", $url);
        }

        return PatientModel::removePatient($id);
    }
}