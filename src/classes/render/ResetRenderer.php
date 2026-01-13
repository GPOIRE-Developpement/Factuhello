<?php

namespace guillaumepaquin\factuhello\render;

/**
 * Classe responsable du rendu des pages
 */
class ResetRenderer
{
    /**
     * Génère le HTML de la page de réinitialisation du mot de passe
     * @return string Contenu de la page de réinitialisation du mot de passe
     */
    public static function render($email, $token): string
    {
        return <<<HTML
            <form action="?action=reset" method="POST" class="form-reset">
                <p>Nouveau mot de passe<p>

                <div class="email">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{$email}" readonly>
                </div>

                <input type="hidden" id="token" name="token" value="{$token}">

                <div class="password">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="password">
                    <label for="confirm-password">Mot de passe</label>
                    <input type="password" id="confirm-password" name="confirm-password" required>
                </div>

                <button type="submit">Enregistrer</button>
            </form>
        HTML;
    }
}