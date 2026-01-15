<?php

namespace guillaumepaquin\factuhello\action;

use guillaumepaquin\factuhello\model\AccountModel;
use guillaumepaquin\factuhello\render\ErrorRenderer;
use guillaumepaquin\factuhello\render\ResetRenderer;

/**
 * Action par défaut : page de changement de mot de passe
 */
class ResetAction extends Action {
    /**
     * Execute lorsqu'il n'y a pas d'action spécifique demandée, retourne la page de changement de mot de passe
     * @return string
     */
    public static function execute(): string {
        // Rediriger vers dashboard si déjà connecté
        if(AccountModel::isLoggedIn()){
            header("Location: ?action=dashboard");
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $email = $_POST['email'] ?? '';
            $token = $_POST['token'] ?? '';

            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm-password'] ?? null;

            if($password !== $confirm_password){
                return ErrorRenderer::render("Les mots de passe ne correspondent pas.", "?action=reset&email=$email&token=$token");
            }

            $password = password_hash($password, PASSWORD_BCRYPT);

            return AccountModel::resetPassword($email, $token, $password);
        }else{
            $email = $_GET['email'] ?? '';
            $token = $_GET['token'] ?? '';

            return ResetRenderer::render($email, $token);
        }
    }
}