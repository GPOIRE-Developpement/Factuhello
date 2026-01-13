<?php

namespace guillaumepaquin\factuhello\action;

use Error;
use guillaumepaquin\factuhello\render\ErrorRenderer;
use guillaumepaquin\factuhello\render\LoginRenderer;

/**
 * Action par défaut : page de connexion
 */
class DefaultAction extends Action {
    /**
     * Execute lorsqu'il n'y a pas d'action spécifique demandée, retourne la page de connexion
     * @return string
     */
    public static function execute(): string {
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            return ErrorRenderer::render("Méthode non autorisée.", "?action=login");
        }else{
            return LoginRenderer::render();
        }
    }
}