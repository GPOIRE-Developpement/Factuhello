<?php

namespace guillaumepaquin\factuhello\action;

/**
 * Action pour supprimer un fichier PDF temporaire
 */
class DeletePdfAction extends Action {
    public function execute(): string {
        if (!isset($_GET['path'])) {
            return json_encode(['error' => 'Chemin manquant']);
        }

        $relativePath = $_GET['path'];
        
        // Vérifier que le chemin est dans /pdf/
        if (strpos($relativePath, '/pdf/') !== 0) {
            return json_encode(['error' => 'Chemin invalide']);
        }

        $fullPath = __DIR__ . '/../../../' . $relativePath;

        // Supprimer le fichier s'il existe
        if (file_exists($fullPath)) {
            unlink($fullPath);
            return json_encode(['success' => true]);
        }

        return json_encode(['error' => 'Fichier introuvable']);
    }
}
