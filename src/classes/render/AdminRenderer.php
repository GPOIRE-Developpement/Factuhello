<?php

namespace guillaumepaquin\factuhello\render;

use guillaumepaquin\factuhello\model\AccountModel;

/**
 * Classe responsable du rendu de la page d'administration
 */
class AdminRenderer {
    /**
     * Génère le HTML de la page d'administration
     * @return string Contenu de la page d'administration
     */
    public static function render(): string {
        $users = AccountModel::getAllUsers();
        $pendingCount = count(array_filter($users, fn($u) => !$u['is_approved']));
        
        $usersRows = self::renderUsersRows($users);

        return <<<HTML
<div class="page-shell">
    <header class="page-header">
        <div>
            <h1 class="page-header-title">Administration</h1>
            <p class="page-header-subtitle">Gestion des utilisateurs et des accès</p>
        </div>
        <div class="board-header-actions">
            <a href="?action=dashboard" class="button button-secondary">← Tableau de bord</a>
            <a href="?action=logout" class="button button-ghost">Déconnexion</a>
        </div>
    </header>

    <main class="layout-board">
        <section class="board-main-card">
            <p>Utilisateurs ({$pendingCount} en attente)</p>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
                $usersRows
            </table>
        </section>
    </main>
</div>
HTML;
    }

    /**
     * Génère les lignes du tableau des utilisateurs
     * @param array $users Liste des utilisateurs
     * @return string Lignes HTML du tableau
     */
    private static function renderUsersRows(array $users): string {
        if (empty($users)) {
            return '<tr><td colspan="5" style="text-align:center;">Aucun utilisateur</td></tr>';
        }

        $rows = '';
        foreach ($users as $user) {
            $rows .= self::renderUserRow($user);
        }
        return $rows;
    }

    /**
     * Génère une ligne pour un utilisateur
     * @param array $user Données de l'utilisateur
     * @return string Ligne HTML
     */
    private static function renderUserRow(array $user): string {
        $id = htmlspecialchars($user['id']);
        $email = htmlspecialchars($user['email']);
        $role = $user['role'];
        $isApproved = $user['is_approved'];

        // Badge pour le rôle
        $roleBadge = $role === 'admin' 
            ? '<span class="badge badge-admin"><span class="badge-dot"></span> Admin</span>'
            : '<span class="badge"><span class="badge-dot"></span> Utilisateur</span>';

        // Badge pour le statut
        $statusBadge = $isApproved 
            ? '<span class="pill pill-success">Approuvé</span>'
            : '<span class="pill pill-pending">En attente</span>';

        // Boutons d'action
        $actions = '';
        if ($role !== 'admin') {
            if (!$isApproved) {
                $actions .= <<<HTML
<form method="POST" style="display:inline;">
    <input type="hidden" name="admin_action" value="approve">
    <input type="hidden" name="user_id" value="$id">
    <button type="submit" class="button button-primary button-small">✓ Approuver</button>
</form>
HTML;
            } else {
                $actions .= <<<HTML
<form method="POST" style="display:inline;">
    <input type="hidden" name="admin_action" value="revoke">
    <input type="hidden" name="user_id" value="$id">
    <button type="submit" class="button button-danger button-small">✗ Révoquer</button>
</form>
HTML;
            }
        } else {
            $actions = '<span class="text-soft">—</span>';
        }

        return <<<HTML
<tr>
    <td>$id</td>
    <td>$email</td>
    <td>$roleBadge</td>
    <td>$statusBadge</td>
    <td>$actions</td>
</tr>
HTML;
    }
}
