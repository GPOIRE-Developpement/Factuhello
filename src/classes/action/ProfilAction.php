<?php

namespace guillaumepaquin\factuhello\action;

use guillaumepaquin\factuhello\model\AccountModel;
use guillaumepaquin\factuhello\render\ProfilRenderer;

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

        return ProfilRenderer::render($id);    
    }
}
