<?php

namespace guillaumepaquin\factuhello\action;

use guillaumepaquin\factuhello\model\InvoiceModel;
use guillaumepaquin\factuhello\model\PatientModel;
use guillaumepaquin\factuhello\render\SuccessRenderer;
use guillaumepaquin\factuhello\render\ErrorRenderer;

/**
 * Action pour renvoyer une facture par email au patient
 */
class ResendInvoiceAction extends Action {
    public static function execute(): string {
        if (!isset($_GET['id']) || !isset($_GET['patient'])) {
            return ErrorRenderer::render("Paramètres manquants", "?action=dashboard");
        }

        $invoiceId = intval($_GET['id']);
        $patientId = intval($_GET['patient']);
        
        try {
            // Récupérer les informations du patient
            $patient = PatientModel::getPatientById($patientId);
            if (!$patient) {
                return ErrorRenderer::render("Patient introuvable", "?action=dashboard");
            }
            
            // Générer le PDF de la facture
            $pdfData = InvoiceModel::generateInvoicePDF($invoiceId);
            $pdfContent = $pdfData['content'];
            $pdfPath = $pdfData['path'];
            
            // Envoyer la facture par email
            $emailSent = InvoiceModel::sendInvoiceByEmailDirect($invoiceId, $patient['email'], $pdfPath, $pdfContent);
            
            // Supprimer le fichier temporaire
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
            
            if ($emailSent) {
                return SuccessRenderer::render(
                    "Facture #$invoiceId renvoyée par email à " . $patient['email'],
                    "?action=profil&id=" . urlencode($patientId)
                );
            } else {
                return ErrorRenderer::render(
                    "Erreur lors de l'envoi de l'email",
                    "?action=profil&id=" . urlencode($patientId)
                );
            }
            
        } catch (\Exception $e) {
            return ErrorRenderer::render(
                "Erreur : " . $e->getMessage(),
                "?action=profil&id=" . urlencode($patientId)
            );
        }
    }
}
