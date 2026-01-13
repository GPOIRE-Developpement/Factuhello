<?php

namespace guillaumepaquin\factuhello\render;

/**
 * Classe responsable du rendu des pages
 */
class RegisterRenderer {
    /**
     * Génère le HTML de la page d'enregistrement
     * @return string Contenu de la page d'enregistrement
     */
    public static function render(): string {
        return <<<HTML
            <form action="?action=register" method="POST" class="form-register">
                <p>Enregistrement<p>

                <div class="email">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="email">
                    <label for="confirm-email">Confirmer Email</label>
                    <input type="email" id="confirm-email" name="confirm-email" required>
                </div>

                <div class="password">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="password">
                    <label for="confirm-password">Mot de passe</label>
                    <input type="password" id="confirm-password" name="confirm-password" required>
                </div>

                <a href="?action=login">Se connecter</button>
                <button type="submit">S'inscrire</button>
            </form>
        HTML;
    }
}