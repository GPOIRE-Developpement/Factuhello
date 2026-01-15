<?php

namespace guillaumepaquin\factuhello\render;

/**
 * Classe responsable du rendu des pages
 */
class PageRenderer {
    /**
     * Génère le HTML complet de la page
     * @param string $content Contenu HTML à afficher
     * @param string $title   Titre de la page
     * @param string $bodyClass Classe CSS optionnelle pour le body
     * @return string Page HTML complète
     */
    public static function render(string $content, string $title = 'Factuhello', string $bodyClass = ''): string {
        $bodyClassAttr = $bodyClass !== '' ? " class=\"{$bodyClass}\"" : '';

        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{$title}</title>
	<link rel="stylesheet" href="style.css">
</head>
<body{$bodyClassAttr}>
{$content}
</body>
</html>
HTML;
    }
}