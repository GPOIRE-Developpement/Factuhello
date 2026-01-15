<?php

namespace guillaumepaquin\factuhello\model;

use guillaumepaquin\factuhello\model\Repository;
use guillaumepaquin\factuhello\model\MailModel;
use guillaumepaquin\factuhello\render\SuccessRenderer;
use guillaumepaquin\factuhello\render\ErrorRenderer;
use guillaumepaquin\factuhello\render\InvoiceTemplateRenderer;

/**
 * Modèle pour la gestion des factures
 */
class InvoiceModel {
    
    /**
     * Génère une facture à partir des consultations sélectionnées
     * @param int $patientId ID du patient
     * @param array $consultationIds Liste des IDs de consultations
     * @param float $reductionPercent Pourcentage de réduction
     * @return string HTML de réponse
     */
    public static function generateInvoice($patientId, $consultationIds, $reductionPercent = 0): string {
        $pdo = Repository::getInstance()->getPdo();

        try {
            // Vérifier que toutes les consultations appartiennent au patient et ne sont pas facturées
            $placeholders = implode(',', array_fill(0, count($consultationIds), '?'));
            $stmt = $pdo->prepare("SELECT SUM(b.price) as total_price FROM consultations c 
                INNER JOIN benefits b ON c.benefit_id = b.id 
                WHERE c.patient_id = ? AND c.id IN ($placeholders) 
                AND c.id NOT IN (SELECT consultation_id FROM invoice_consultations)");
            
            $params = array_merge([$patientId], $consultationIds);
            $stmt->execute($params);
            
            $row = $stmt->fetch();
            if (!$row || $row['total_price'] === null) {
                return ErrorRenderer::render(
                    "Erreur : consultations introuvables ou déjà facturées.",
                    "?action=profil&id=" . urlencode($patientId)
                );
            }

            // Calculer le montant total avec réduction
            $totalPrice = floatval($row['total_price']);
            $reductionAmount = $totalPrice * ($reductionPercent / 100);
            $finalAmount = $totalPrice - $reductionAmount;

            // Insérer la facture
            $stmt = $pdo->prepare("INSERT INTO invoices (total_amount, created_at) VALUES (:total_amount, NOW())");
            $stmt->execute([
                ':total_amount' => $finalAmount
            ]);

            $invoiceId = $pdo->lastInsertId();

            // Récupérer les informations du patient pour l'email
            $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = :patient_id");
            $stmt->execute([':patient_id' => $patientId]);
            $patient = $stmt->fetch();

            // Lier les consultations à la facture
            $stmt = $pdo->prepare("INSERT INTO invoice_consultations (invoice_id, consultation_id) VALUES (:invoice_id, :consultation_id)");
            foreach ($consultationIds as $consultationId) {
                $stmt->execute([
                    ':invoice_id' => $invoiceId,
                    ':consultation_id' => $consultationId
                ]);
            }

            // Générer le PDF de la facture
            try {
                // Générer le PDF et retourner son contenu ET son chemin
                $pdfData = self::generateInvoicePDF($invoiceId);
                $pdfFullPath = $pdfData['path'];
                $pdfContent = $pdfData['content'];
                $pdfFileName = basename($pdfFullPath);
                
                // Envoyer la facture par email au patient avec le PDF
                $emailSent = self::sendInvoiceByEmailDirect($invoiceId, $patient['email'], $pdfFullPath, $pdfContent);
                
                if ($emailSent) {
                    $pdfMessage = "Email envoyé à " . $patient['email'];
                } else {
                    $pdfMessage = "Erreur lors de l'envoi de l'email";
                }
                
                // Garder le fichier PDF pour le téléchargement via bouton
                $downloadUrl = "?action=download-pdf&file=" . urlencode($pdfFileName);
                
            } catch (\Exception $e) {
                $pdfMessage = "Erreur lors de la génération du PDF : " . $e->getMessage();
                $downloadUrl = null;
            }

            return SuccessRenderer::renderWithDownload(
                "Facture #$invoiceId générée avec succès. Montant: $finalAmount €",
                $pdfMessage,
                $downloadUrl,
                "?action=profil&id=" . urlencode($patientId)
            );

        } catch (\PDOException $e) {
            return ErrorRenderer::render(
                "Erreur lors de la génération de la facture : " . $e->getMessage(),
                "?action=profil&id=" . urlencode($patientId)
            );
        }
    }

    /**
     * Récupère une facture par ID
     * @param int $invoiceId ID de la facture
     * @return array|null Données de la facture
     */
    public static function getInvoiceById($invoiceId) {
        $pdo = Repository::getInstance()->getPdo();

        try {
            $stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = :id");
            $stmt->execute([':id' => $invoiceId]);

            if ($stmt->rowCount() == 0) {
                return null;
            }

            return $stmt->fetch();
        } catch (\PDOException $e) {
            return null;
        }
    }

    /**
     * Récupère toutes les consultations d'une facture
     * @param int $invoiceId ID de la facture
     * @return array Consultations de la facture
     */
    public static function getConsultationsByInvoiceId($invoiceId): array {
        $pdo = Repository::getInstance()->getPdo();
        $consultations = [];

        try {
            $stmt = $pdo->prepare("SELECT c.id, c.date, b.name as benefit_name, b.price as benefit_price
                FROM consultations c
                INNER JOIN benefits b ON c.benefit_id = b.id
                INNER JOIN invoice_consultations ic ON c.id = ic.consultation_id
                WHERE ic.invoice_id = :invoice_id
                ORDER BY c.date DESC");
            $stmt->execute([
                ':invoice_id' => $invoiceId
            ]);

            while ($row = $stmt->fetch()) {
                $consultations[] = $row;
            }

            return $consultations;
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Supprime une facture et restaure les consultations comme non facturées
     * @param int $invoiceId ID de la facture
     * @return string HTML de réponse
     */
    public static function deleteInvoice($invoiceId): string {
        $pdo = Repository::getInstance()->getPdo();

        try {
            // Récupérer le patient associé
            $stmt = $pdo->prepare("SELECT c.patient_id FROM consultations c
                INNER JOIN invoice_consultations ic ON c.id = ic.consultation_id
                WHERE ic.invoice_id = :invoice_id LIMIT 1");
            $stmt->execute([':invoice_id' => $invoiceId]);

            if ($stmt->rowCount() == 0) {
                return ErrorRenderer::render("Facture introuvable.", "?action=dashboard");
            }

            $row = $stmt->fetch();
            $patientId = $row['patient_id'];

            // Supprimer les liens invoice_consultations
            $stmt = $pdo->prepare("DELETE FROM invoice_consultations WHERE invoice_id = :invoice_id");
            $stmt->execute([':invoice_id' => $invoiceId]);

            // Supprimer la facture
            $stmt = $pdo->prepare("DELETE FROM invoices WHERE id = :invoice_id");
            $stmt->execute([':invoice_id' => $invoiceId]);

            return SuccessRenderer::render(
                "Facture supprimée avec succès.",
                "?action=profil&id=" . urlencode($patientId)
            );

        } catch (\PDOException $e) {
            return ErrorRenderer::render(
                "Erreur lors de la suppression de la facture : " . $e->getMessage(),
                "?action=dashboard"
            );
        }
    }

    public static function generateInvoicePDF($invoiceId): array {
        $pdo = Repository::getInstance()->getPdo();

        try {
            // Récupérer les informations de la facture
            $invoice = self::getInvoiceById($invoiceId);
            if (!$invoice) {
                throw new \Exception("Facture introuvable");
            }

            // Récupérer les consultations de la facture
            $consultations = self::getConsultationsByInvoiceId($invoiceId);
            if (empty($consultations)) {
                throw new \Exception("Aucune consultation trouvée pour cette facture");
            }

            // Récupérer les informations du patient via la première consultation
            $stmt = $pdo->prepare("SELECT p.* FROM patients p
                INNER JOIN consultations c ON p.id = c.patient_id
                INNER JOIN invoice_consultations ic ON c.id = ic.consultation_id
                WHERE ic.invoice_id = :invoice_id LIMIT 1");
            $stmt->execute([':invoice_id' => $invoiceId]);
            $patient = $stmt->fetch();

            if (!$patient) {
                throw new \Exception("Patient introuvable");
            }

            // Créer le dossier pdf s'il n'existe pas
            $pdfDir = __DIR__ . '/../../../pdf';
            if (!is_dir($pdfDir)) {
                mkdir($pdfDir, 0755, true);
            }

            // Nettoyer les anciens fichiers PDF (plus de 5 minutes)
            self::cleanOldPdfFiles();

            // Générer le contenu des consultations
            $consultationsHTML = "";
            $totalBeforeReduction = 0;
            foreach ($consultations as $consultation) {
                $consultationsHTML .= sprintf(
                    "<tr><td>%s</td><td>%s</td><td>%.2f €</td></tr>\n",
                    $consultation['date'],
                    $consultation['benefit_name'],
                    $consultation['benefit_price']
                );
                $totalBeforeReduction += $consultation['benefit_price'];
            }

            // Calculer la réduction
            $finalAmount = $invoice['total_amount'];
            $reductionAmount = $totalBeforeReduction - $finalAmount;
            $reductionPercent = $totalBeforeReduction > 0 ? ($reductionAmount / $totalBeforeReduction) * 100 : 0;

            // Utiliser le renderer pour générer le HTML de la facture
            $htmlContent = InvoiceTemplateRenderer::render(
                $invoiceId,
                date('d/m/Y', strtotime($invoice['created_at'])),
                $patient['name'],
                $patient['email'],
                $patient['phone'],
                $patient['address'],
                $consultationsHTML,
                number_format($totalBeforeReduction, 2, ',', ' '),
                number_format($reductionPercent, 2, ',', ' '),
                number_format($reductionAmount, 2, ',', ' '),
                number_format($finalAmount, 2, ',', ' ')
            );

            // Générer le nom du fichier PDF
            $pdfFileName = "facture_" . $invoiceId . "_" . date('YmdHis') . ".pdf";
            $pdfFilePath = $pdfDir . '/' . $pdfFileName;

            // Utiliser Dompdf pour convertir HTML en PDF
            try {
                // Créer le dossier temporaire pour Dompdf
                $tempDir = __DIR__ . '/../../../temp';
                if (!is_dir($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }
                
                $options = new \Dompdf\Options();
                $options->set('tempDir', $tempDir);
                $options->set('isRemoteEnabled', false);
                $options->set('isHtml5ParserEnabled', true);
                $options->set('fontDir', $tempDir . '/fonts');
                $options->set('fontCache', $tempDir . '/fonts');
                
                $dompdf = new \Dompdf\Dompdf($options);
                $dompdf->loadHtml($htmlContent, 'UTF-8');
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                
                $pdfContent = $dompdf->output();
                
                if (empty($pdfContent)) {
                    throw new \Exception("Dompdf n'a généré aucun contenu");
                }
                
                // Vérifier que c'est bien du PDF
                if (strpos($pdfContent, '%PDF') === false) {
                    throw new \Exception("Le contenu généré n'est pas un PDF valide");
                }
                
                file_put_contents($pdfFilePath, $pdfContent);
                
            } catch (\Exception $e) {
                throw new \Exception("Erreur Dompdf : " . $e->getMessage());
            }

            // Retourner le chemin et le contenu du PDF
            return [
                'path' => $pdfFilePath,
                'content' => $pdfContent
            ];

        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de la génération du PDF : " . $e->getMessage());
        }
    }

    public static function sendInvoiceByEmailDirect($invoiceId, $email, $pdfFilePath, $pdfContent): bool {
        try {
            // Récupérer les informations de la facture
            $invoice = self::getInvoiceById($invoiceId);
            if (!$invoice) {
                return false;
            }

            // Préparer le sujet et le corps de l'email
            $subject = "Votre facture #" . $invoiceId;
            $body = "Bonjour,<br><br>";
            $body .= "Veuillez trouver ci-joint votre facture #" . $invoiceId . ".<br>";
            $body .= "Montant total : " . number_format($invoice['total_amount'], 2, ',', ' ') . " €<br>";
            $body .= "Date : " . date('d/m/Y', strtotime($invoice['created_at'])) . "<br><br>";
            $body .= "Merci de votre confiance.<br><br>";
            $body .= "Cordialement";

            // Envoyer l'email avec le contenu PDF en pièce jointe
            return MailModel::sendEmailWithPdfContent($email, $subject, $body, basename($pdfFilePath), $pdfContent);

        } catch (\Exception $e) {
            error_log("Erreur lors de l'envoi de la facture par email : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Nettoie les fichiers PDF plus vieux que 5 minutes
     */
    public static function cleanOldPdfFiles(): void {
        $pdfDir = __DIR__ . '/../../../pdf';
        
        if (!is_dir($pdfDir)) {
            return;
        }

        $maxAge = 5 * 60; // 5 minutes en secondes
        $now = time();

        $files = glob($pdfDir . '/*.pdf');
        foreach ($files as $file) {
            if (is_file($file)) {
                $fileAge = $now - filemtime($file);
                if ($fileAge > $maxAge) {
                    unlink($file);
                }
            }
        }
    }
}
