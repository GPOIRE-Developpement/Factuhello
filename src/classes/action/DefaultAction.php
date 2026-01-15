<?php

namespace guillaumepaquin\factuhello\action;

use Error;
use guillaumepaquin\factuhello\render\ErrorRenderer;
use guillaumepaquin\factuhello\render\LoginRenderer;
use guillaumepaquin\factuhello\model\AccountModel;

/**
 * Action par défaut : page de connexion
 */
class DefaultAction extends Action {
    /**
     * Execute lorsqu'il n'y a pas d'action spécifique demandée, retourne la page de connexion
     * @return string
     */
    public static function execute(): string {
        // Rediriger vers dashboard si déjà connecté
        if(AccountModel::isLoggedIn()){
            header("Location: ?action=dashboard");
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            return ErrorRenderer::render("Méthode non autorisée.", "?action=login");
        }else{
            return LoginRenderer::render();
        }
    }
}