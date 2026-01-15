<?php

namespace guillaumepaquin\factuhello\action;

/**
 * Action pour télécharger un fichier PDF
 */
class DownloadPdfAction extends Action {
    public static function execute(): string {
        if (!isset($_GET['file'])) {
            return "Fichier non spécifié";
        }

        $fileName = basename($_GET['file']); // Sécurité : éviter les chemins relatifs
        $pdfDir = __DIR__ . '/../../../pdf';
        $pdfPath = $pdfDir . '/' . $fileName;
        
        // Vérifier que le fichier existe
        if (!file_exists($pdfPath)) {
            return "Fichier PDF introuvable : $fileName";
        }

        // Lire le contenu du PDF
        $pdfContent = file_get_contents($pdfPath);

        // Vérifier que c'est bien un PDF
        if (strpos($pdfContent, '%PDF') === false) {
            return "Le fichier n'est pas un PDF valide";
        }

        // Nettoyer tout buffer de sortie
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Définir les en-têtes pour le téléchargement PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . strlen($pdfContent));
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

        // Envoyer le contenu
        echo $pdfContent;
        
        // Supprimer le fichier après téléchargement
        unlink($pdfPath);
        
        exit;
    }
}

