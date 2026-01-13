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
            <div class="error-message">
                <p>{$message}</p>
                <a href="{$return}">Retour</a>
            </div>
        HTML;
    }
}