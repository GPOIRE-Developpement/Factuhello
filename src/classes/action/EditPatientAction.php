<?php

namespace guillaumepaquin\factuhello\action;

use guillaumepaquin\factuhello\model\AccountModel;
use guillaumepaquin\factuhello\render\ErrorRenderer;
use guillaumepaquin\factuhello\model\PatientModel;

/**
 * Action par défaut : page d'ajout d'un patient
 */
class EditPatientAction extends Action {
    /**
     * Execute lorsqu'il n'y a pas d'action spécifique demandée, retourne la page d'ajout d'un patient
     * @return string
     */
    public static function execute(): string {
        if(!AccountModel::isLoggedIn()){
            header("Location: ?action=login");
            exit();
        }

        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? null;
        $email = $_POST['email'] ?? null;
        $phone = $_POST['phone'] ?? null;
        $address = $_POST['address'] ?? null;

        if(!isset($id) || !isset($name) || !isset($email) || !isset($phone) || !isset($address)){
            $url = "?action=dashboard";
            if(isset($id)){
                $url = "?action=profil&id=" . urlencode($id);
            }

            return ErrorRenderer::render("Tous les champs sont obligatoires.", $url);
        }

        return PatientModel::editPatient($id, $name, $email, $phone, $address);
    }
}