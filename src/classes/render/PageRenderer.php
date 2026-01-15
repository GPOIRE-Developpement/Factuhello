<?php

namespace guillaumepaquin\factuhello\render;

/**
 * Classe responsable du rendu des pages
 */
class PageRenderer {
    /**
     * Génère le HTML complet de la page
     * @param string $content Contenu HTML à afficher
     * @return string Page HTML complète
     */
    public static function render(string $content): string {
        return <<<HTML
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Factuhello</title>
            </head>
            <body>
                {$content}
            </body>
            </html>
        HTML;
    }
}