<?php

namespace guillaumepaquin\factuhello\action;

use guillaumepaquin\factuhello\render\ErrorRenderer;
use guillaumepaquin\factuhello\render\RegisterRenderer;
use guillaumepaquin\factuhello\model\AccountModel;

/**
 * Action par défaut : page d'enregistrement
 */
class RegisterAction extends Action {
    /**
     * Execute lorsqu'il n'y a pas d'action spécifique demandée, retourne la page d'enregistrement
     * @return string
     */
    public static function execute(): string {
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $email = $_POST['email'] ?? '';
            $confirm_email = $_POST['confirm-email'] ?? null;

            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm-password'] ?? null;

            if($email == '' || $password == ''){
                return ErrorRenderer::render("L'email et le mot de passe sont obligatoires.", "?action=register");
            }

            if($email != $confirm_email || $password != $confirm_password){
                return ErrorRenderer::render("Les emails et les mots de passe ne correspondent pas.", "?action=register");
            }

            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            return AccountModel::register($email, $passwordHash);
        }else{
            return RegisterRenderer::render();
        }
    }
}