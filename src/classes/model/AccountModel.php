<?php

namespace guillaumepaquin\factuhello\model;

use guillaumepaquin\factuhello\model\Repository;
use guillaumepaquin\factuhello\model\MailModel;
use guillaumepaquin\factuhello\render\SuccessRenderer;
use guillaumepaquin\factuhello\render\ErrorRenderer;

class AccountModel {
    // Méthode pour se connecter
    public static function login($email, $password): string {
        $pdo = Repository::getInstance()->getPdo();

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);

            if ($stmt->rowCount() == 0) {
                return ErrorRenderer::render("Email ou mot de passe incorrect.", "?action=login");
            }

            $user = $stmt->fetch();

            if (!password_verify($password, $user['password_hash'])) {
                self::incorrectPassword();
                return ErrorRenderer::render("Email ou mot de passe incorrect.", "?action=login");
            }

            // Vérifier si le compte est approuvé
            if (!$user['is_approved']) {
                return ErrorRenderer::render("Votre compte est en attente d'approbation par un administrateur.", "?action=login");
            }

            self::correctPassword();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['is_approved'] = $user['is_approved'];

            return SuccessRenderer::render("Connexion Réussie : {$email}", "?action=dashboard");
        } catch (\PDOException $e) {
            return ErrorRenderer::render("Erreur lors de la connexion : erreur de base de données.", "?action=login");
        }
    }

    public static function incorrectPassword(){
        $_SESSION['tryedLogin'] = $_SESSION['tryedLogin'] + 1;
        $_SESSION['lastTryedLogin'] = time();
        return ErrorRenderer::render("Email ou mot de passe incorrect.", "?action=login");
    }

    public static function correctPassword(){
        $_SESSION['tryedLogin'] = 0;
        $_SESSION['lastTryedLogin'] = time() - 60;
    }

    public static function canTryLogin(){
        if (!isset($_SESSION['tryedLogin'])) {
            $_SESSION['tryedLogin'] = 0;
        }

        if (!isset($_SESSION['lastTryedLogin'])) {
            $_SESSION['lastTryedLogin'] = time();
        }

        return $_SESSION['tryedLogin'] < 3 || (time() - $_SESSION['lastTryedLogin']) > 60;
    }

    // Méthode pour s'enregistrer
    public static function register($email, $password): string{
        $pdo = Repository::getInstance()->getPdo();

        try {
            // Vérifier s'il existe déjà un admin (premier utilisateur = admin auto-approuvé)
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users");
            $stmt->execute();
            $row = $stmt->fetch();
            
            if ($row['count'] == 0) {
                // Premier utilisateur : admin auto-approuvé
                $stmt = $pdo->prepare("INSERT INTO users(email, password_hash, role, is_approved) VALUES (:email, :password, 'admin', 1)");
                $stmt->execute([':email' => $email, ':password' => $password]);
                return SuccessRenderer::render("Compte administrateur créé avec succès pour : {$email}", "?action=login");
            } else {
                // Utilisateur normal : non approuvé
                $stmt = $pdo->prepare("INSERT INTO users(email, password_hash, role, is_approved) VALUES (:email, :password, 'user', 0)");
                $stmt->execute([':email' => $email, ':password' => $password]);
                return SuccessRenderer::render("Inscription réussie ! Votre compte est en attente d'approbation par un administrateur.", "?action=login");
            }
        } catch (\PDOException $e) {
            return ErrorRenderer::render("Erreur lors de l'inscription : l'email existe déjà ou erreur de base de données.", "?action=register");
        }
    }

    // Méthode pour réinitialiser le mot de passe (envoie d'un mail)
    public static function resetPasswordRequest($email): string{
        $pdo = Repository::getInstance()->getPdo();

        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);

            if ($stmt->rowCount() == 0) {
                return SuccessRenderer::render("Si cet email existe, un lien de réinitialisation a été envoyé.", "?action=login");
            }
        } catch (\PDOException $e) {
            return ErrorRenderer::render("Erreur lors de la réinitialisation du mot de passe.", "?action=forgot");
        }

        $token = bin2hex(random_bytes(32));

        try {
            $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 20 MINUTE) WHERE email = :email");
            $stmt->execute([':token' => $token, ':email' => $email]);
        } catch (\PDOException $e) {
            return ErrorRenderer::render("Erreur lors de la réinitialisation du mot de passe : erreur de base de données.", "?action=forgot");
        }

        $emailSent = MailModel::sendPasswordResetEmail($email, $token);

        if (!$emailSent) {
            return ErrorRenderer::render("Erreur lors de l'envoi de l'email. Veuillez réessayer.", "?action=forgot");
        }

        return SuccessRenderer::render("Un email de réinitialisation a été envoyé à : {$email}", "?action=login");
    }

    // Méthode pour changer le mot de passe (lien dans le mail)
    public static function resetPassword($email, $token, $newPassword): string {
        $pdo = Repository::getInstance()->getPdo();

        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND reset_token = :token AND reset_token_expiry > NOW()");
            $stmt->execute([':email' => $email, ':token' => $token]);

            if ($stmt->rowCount() == 0) {
                return ErrorRenderer::render("Lien de réinitialisation invalide ou expiré.", "?action=forgot");
            }

            $stmt = $pdo->prepare("UPDATE users SET password_hash = :password, reset_token = NULL, reset_token_expiry = NULL WHERE email = :email");
            $stmt->execute([':password' => $newPassword, ':email' => $email]);

            return SuccessRenderer::render("Mot de passe réinitialisé avec succès pour l'email : {$email}", "?action=login");
        } catch (\PDOException $e) {
            return ErrorRenderer::render("Erreur lors de la réinitialisation du mot de passe : erreur de base de données.", "?action=forgot");
        }
    }

    // Méthode pour voir si l'utilisateur est connecté
    public static function isLoggedIn(): bool{
        return isset($_SESSION['user_id']);
    }

    public static function logout(): string{
        session_unset();
        session_destroy();

        return SuccessRenderer::render("Déconnexion réussie.", "?action=login");
    }

    // Méthode pour obtenir les informations de l'utilisateur
    public static function getUser(): array {
        $tab = [];
        $tab['id'] = $_SESSION['user_id'] ?? null;
        $tab['email'] = $_SESSION['user_email'] ?? null;
        $tab['role'] = $_SESSION['user_role'] ?? 'user';
        $tab['is_approved'] = $_SESSION['is_approved'] ?? 0;

        return $tab;
    }

    // Méthode pour vérifier si l'utilisateur est admin
    public static function isAdmin(): bool {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    // Méthode pour obtenir les utilisateurs en attente d'approbation
    public static function getPendingUsers(): array {
        $pdo = Repository::getInstance()->getPdo();
        $users = [];

        try {
            $stmt = $pdo->prepare("SELECT id, email, role, is_approved FROM users WHERE is_approved = 0 ORDER BY id DESC");
            $stmt->execute();

            while ($row = $stmt->fetch()) {
                $users[] = $row;
            }

            return $users;
        } catch (\PDOException $e) {
            return [];
        }
    }

    // Méthode pour obtenir tous les utilisateurs (pour l'admin)
    public static function getAllUsers(): array {
        $pdo = Repository::getInstance()->getPdo();
        $users = [];

        try {
            $stmt = $pdo->prepare("SELECT id, email, role, is_approved FROM users ORDER BY role DESC, is_approved DESC, id ASC");
            $stmt->execute();

            while ($row = $stmt->fetch()) {
                $users[] = $row;
            }

            return $users;
        } catch (\PDOException $e) {
            return [];
        }
    }

    // Méthode pour approuver un utilisateur
    public static function approveUser($userId): string {
        $pdo = Repository::getInstance()->getPdo();

        try {
            $stmt = $pdo->prepare("UPDATE users SET is_approved = 1 WHERE id = :id");
            $stmt->execute([':id' => $userId]);

            return SuccessRenderer::render("Utilisateur approuvé avec succès.", "?action=admin");
        } catch (\PDOException $e) {
            return ErrorRenderer::render("Erreur lors de l'approbation de l'utilisateur.", "?action=admin");
        }
    }

    // Méthode pour révoquer l'accès d'un utilisateur
    public static function revokeUser($userId): string {
        $pdo = Repository::getInstance()->getPdo();

        try {
            // Ne pas permettre de révoquer un admin
            $stmt = $pdo->prepare("SELECT role FROM users WHERE id = :id");
            $stmt->execute([':id' => $userId]);
            $user = $stmt->fetch();

            if ($user && $user['role'] === 'admin') {
                return ErrorRenderer::render("Impossible de révoquer un administrateur.", "?action=admin");
            }

            $stmt = $pdo->prepare("UPDATE users SET is_approved = 0 WHERE id = :id");
            $stmt->execute([':id' => $userId]);

            return SuccessRenderer::render("Accès de l'utilisateur révoqué.", "?action=admin");
        } catch (\PDOException $e) {
            return ErrorRenderer::render("Erreur lors de la révocation de l'utilisateur.", "?action=admin");
        }
    }

    // Méthode pour changer le rôle d'un utilisateur
    public static function changeUserRole($userId, $newRole): string {
        $pdo = Repository::getInstance()->getPdo();

        // Vérifier que le rôle est valide
        if (!in_array($newRole, ['admin', 'user'])) {
            return ErrorRenderer::render("Rôle invalide.", "?action=admin");
        }

        try {
            // Ne pas permettre de changer son propre rôle
            if ($userId == $_SESSION['user_id']) {
                return ErrorRenderer::render("Vous ne pouvez pas modifier votre propre rôle.", "?action=admin");
            }

            $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
            $stmt->execute([':role' => $newRole, ':id' => $userId]);

            $roleLabel = $newRole === 'admin' ? 'Administrateur' : 'Utilisateur';
            return SuccessRenderer::render("Rôle modifié en : $roleLabel", "?action=admin");
        } catch (\PDOException $e) {
            return ErrorRenderer::render("Erreur lors du changement de rôle.", "?action=admin");
        }
    }
}