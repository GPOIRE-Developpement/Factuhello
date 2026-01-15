<?php

namespace guillaumepaquin\factuhello\action;

use guillaumepaquin\factuhello\model\InvoiceModel;

/**
 * Action pour télécharger une facture existante en PDF
 */
class DownloadInvoiceAction extends Action {
    public static function execute(): string {
        if (!isset($_GET['id'])) {
            return "ID de facture non spécifié";
        }

        $invoiceId = intval($_GET['id']);
        
        try {
            // Générer le PDF de la facture
            $pdfData = InvoiceModel::generateInvoicePDF($invoiceId);
            $pdfContent = $pdfData['content'];
            $pdfPath = $pdfData['path'];
            
            // Nettoyer tout buffer de sortie
            while (ob_get_level()) {
                ob_end_clean();
            }

            // Définir les en-têtes pour le téléchargement PDF
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="facture_' . $invoiceId . '.pdf"');
            header('Content-Length: ' . strlen($pdfContent));
            header('Pragma: no-cache');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

            // Envoyer le contenu
            echo $pdfContent;
            
            // Supprimer le fichier temporaire
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
            
            exit;
            
        } catch (\Exception $e) {
            return "Erreur lors de la génération du PDF : " . $e->getMessage();
        }
    }
}
