<?php

namespace guillaumepaquin\factuhello\render;

/**
 * Classe responsable du rendu des pages
 */
class ErrorRenderer {
    /**
     * Génère le HTML de la page d'échec
     * @return string Contenu de la page d'échec
     */
    public static function render($message, $return): string {
        return <<<HTML
<div class="login-container">
    <div class="form-login">
        <p class="title">Erreur</p>
        <div class="description-container">
            <p class="sub-title">{$message}</p>
        </div>
        <div class="action-container">
            <a href="{$return}" class="button button-danger">Retour</a>
        </div>
    </div>
</div>
HTML;
    }
}