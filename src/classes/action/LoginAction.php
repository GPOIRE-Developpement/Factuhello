<?php

namespace guillaumepaquin\factuhello\action;

use guillaumepaquin\factuhello\model\AccountModel;
use guillaumepaquin\factuhello\render\ErrorRenderer;
use guillaumepaquin\factuhello\render\LoginRenderer;

/**
 * Action par défaut : page de connexion
 */
class LoginAction extends Action {
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
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                return ErrorRenderer::render("L'email ou le mot de passe sont incorrectes.", "?action=login");
            }

            if($email == '' || $password == ''){
                return ErrorRenderer::render("L'email ou le mot de passe sont incorrectes.", "?action=login");
            }

            if(!AccountModel::canTryLogin()){
                return ErrorRenderer::render("Trop de tentatives de connexion. Veuillez réessayer plus tard.", "?action=login");
            }

            return AccountModel::login($email, $password);
        }else{
            return LoginRenderer::render();
        }
    }
}