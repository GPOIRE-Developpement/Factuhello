<?php

namespace guillaumepaquin\factuhello\render;

/**
 * Classe responsable du rendu des pages
 */
class LoginRenderer {
    /**
     * Génère le HTML de la page de connexion
     * @return string Contenu de la page de connexion
     */
    public static function render(): string {
        return <<<HTML
<div class="login-container">
    <form action="?action=login" method="POST" class="form-login">
        <p class="title">Connexion</p>
        <div class="description-container">
            <p class="sub-title">Bienvenue sur Factuhello qui permet à un praticient de rédiger ses factures et de suivre ses patients simplement.</p>
        </div>

        <div class="email">
            <input type="email" id="email" name="email" placeholder="Email" required>
        </div>

        <div class="password">
            <input type="password" id="password" name="password" placeholder="Mot de passe" required>
        </div>

        <a class="link" href="?action=forgot">Mot de passe oublié ?</a>

        <div class="action-container">
            <button type="submit" class="button button-primary">Se connecter</button>
            <a href="?action=register" class="button button-secondary">S'inscrire</a>
        </div>
    </form>
</div>
HTML;
    }
}