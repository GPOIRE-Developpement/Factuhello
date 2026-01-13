<?php

namespace guillaumepaquin\factuhello\action;

use guillaumepaquin\factuhello\render\ForgotRenderer;
use guillaumepaquin\factuhello\render\ErrorRenderer;
use guillaumepaquin\factuhello\model\AccountModel;

/**
 * Action par défaut : page de mot de passe oublié
 */
class ForgotAction extends Action {
    /**
     * Execute lorsqu'il n'y a pas d'action spécifique demandée, retourne la page de mot de passe oublié
     * @return string
     */
    public static function execute(): string {
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $email = $_POST['email'] ?? '';
            $confirm_email = $_POST['confirm-email'] ?? null;

            if($email != $confirm_email){
                return ErrorRenderer::render("Les emails et les mots de passe ne correspondent pas.", "?action=register");
            }

            $email = filter_var($email, FILTER_SANITIZE_EMAIL);

            return AccountModel::resetPasswordRequest($email);
        }else{
            return ForgotRenderer::render();
        }
    }
}