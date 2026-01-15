<?php

namespace guillaumepaquin\factuhello\action;

use guillaumepaquin\factuhello\model\AccountModel;
use guillaumepaquin\factuhello\render\AdminRenderer;
use guillaumepaquin\factuhello\render\ErrorRenderer;

/**
 * Action d'administration : gestion des utilisateurs
 */
class AdminAction extends Action {
    /**
     * Affiche la page d'administration ou traite les actions admin
     * @return string
     */
    public static function execute(): string {
        // Vérifier si l'utilisateur est connecté et admin
        if (!AccountModel::isLoggedIn()) {
            header("Location: ?action=login");
            exit();
        }

        if (!AccountModel::isAdmin()) {
            return ErrorRenderer::render("Accès refusé. Vous devez être administrateur.", "?action=dashboard");
        }

        // Traiter les actions POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $action = $_POST['admin_action'] ?? '';
            $userId = $_POST['user_id'] ?? null;

            if ($action === 'approve' && $userId) {
                return AccountModel::approveUser($userId);
            }

            if ($action === 'revoke' && $userId) {
                return AccountModel::revokeUser($userId);
            }
        }

        // Afficher la page d'administration
        return AdminRenderer::render();
    }
}
