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
            <form action="?action=login" method="POST" class="form-login">
                <p>Connexion<p>

                <div class="email">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="password">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <a href="?action=forgot">Mot de passe oublié ?</a>

                <button type="submit">Se connecter</button>
                <a href="?action=register">S'inscrire</button>
            </form>
        HTML;
    }
}