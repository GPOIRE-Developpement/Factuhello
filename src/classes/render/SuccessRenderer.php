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
<div class="login-container">
    <div class="form-login">
        <p class="title">Succès</p>
        <div class="description-container">
            <p class="sub-title">{$message}</p>
        </div>
        <div class="action-container">
            <a href="{$return}" class="button button-primary">Continuer</a>
        </div>
    </div>
</div>
HTML;
    }

    /**
     * Génère le HTML de la page de succès avec un bouton de téléchargement
     * @param string $message Message principal
     * @param string $emailMessage Message concernant l'email
     * @param string|null $downloadUrl URL de téléchargement du PDF
     * @param string $return URL de retour
     * @return string Contenu de la page de succès
     */
    public static function renderWithDownload($message, $emailMessage, $downloadUrl, $return): string {
        $downloadButton = '';
        if ($downloadUrl) {
            $downloadButton = '<a href="' . $downloadUrl . '" class="button button-secondary">Télécharger la facture PDF</a>';
        }

        return <<<HTML
<div class="login-container">
    <div class="form-login">
        <p class="title">Succès</p>
        <div class="description-container">
            <p class="sub-title">{$message}</p>
            <p class="sub-title">{$emailMessage}</p>
        </div>
        <div class="action-container">
            {$downloadButton}
            <a href="{$return}" class="button button-primary">Retour au profil</a>
        </div>
    </div>
</div>
HTML;
    }
}