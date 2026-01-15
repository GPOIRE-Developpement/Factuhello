<?php

namespace guillaumepaquin\factuhello\render;

/**
 * Classe responsable du rendu des pages
 */
class ForgotRenderer
{
    /**
     * Génère le HTML de la page de mot de passe oublié
     * @return string Contenu de la page de mot de passe oublié
     */
    public static function render(): string
    {
        return <<<HTML
<form action="?action=forgot" method="POST" class="form-example">
    <p>Mot de passe oublié</p>

    <div class="email">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
    </div>

    <div class="email">
        <label for="confirm-email">Confirmer Email</label>
        <input type="email" id="confirm-email" name="confirm-email" required>
    </div>

    <button type="submit" class="button button-primary">Réinitialiser</button>
    <a href="?action=login" class="link">Retour</a>
</form>
HTML;
    }
}