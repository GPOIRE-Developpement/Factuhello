<?php

namespace guillaumepaquin\factuhello\action;

/**
 * Action de déconnexion
 */
class LogoutAction extends Action {
    /**
     * Déconnecte l'utilisateur et redirige vers la page de connexion
     * @return string
     */
    public static function execute(): string {
        // Détruire la session
        session_unset();
        session_destroy();
        
        // Rediriger vers la page de connexion
        header("Location: ?action=login");
        exit();
    }
}
