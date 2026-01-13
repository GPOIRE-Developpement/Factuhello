<?php

namespace guillaumepaquin\factuhello\render;

/**
 * Classe responsable du rendu des pages
 */
class SuccessRenderer {
    /**
     * Génère le HTML de la page de succès
     * @return string Contenu de la page de succès
     */
    public static function render($message, $return): string {
        return <<<HTML
            <div class="success-message">
                <p>{$message}</p>
                <a href="{$return}">Continuer</a>
            </div>
        HTML;
    }
}