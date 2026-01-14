<?php

namespace guillaumepaquin\factuhello\model;

use guillaumepaquin\factuhello\model\Repository;
use guillaumepaquin\factuhello\render\SuccessRenderer;
use guillaumepaquin\factuhello\render\ErrorRenderer;

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

            // Lier les consultations à la facture
            $stmt = $pdo->prepare("INSERT INTO invoice_consultations (invoice_id, consultation_id) VALUES (:invoice_id, :consultation_id)");
            foreach ($consultationIds as $consultationId) {
                $stmt->execute([
                    ':invoice_id' => $invoiceId,
                    ':consultation_id' => $consultationId
                ]);
            }

            return SuccessRenderer::render(
                "Facture #$invoiceId générée avec succès. Montant: $finalAmount €",
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
}
