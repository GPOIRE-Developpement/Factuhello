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
            $downloadButton = '<a href="' . $downloadUrl . '" class="download-btn" style="display: inline-block; margin: 10px; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;">📄 Télécharger la facture PDF</a>';
        }
        
        return <<<HTML
            <div class="success-message">
                <p>{$message}</p>
                <p>{$emailMessage}</p>
                {$downloadButton}
                <br><br>
                <a href="{$return}">Retour au profil</a>
            </div>
        HTML;
    }
}