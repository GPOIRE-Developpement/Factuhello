<?php

namespace guillaumepaquin\factuhello\render;

use guillaumepaquin\factuhello\model\PatientModel;
use guillaumepaquin\factuhello\render\EditPatientRenderer;
use guillaumepaquin\factuhello\render\RemovePatientRenderer;
use guillaumepaquin\factuhello\render\AddConsultationRenderer;
use guillaumepaquin\factuhello\render\SelectConsultRenderer;

/**
 * Classe responsable du rendu de la page profil
 */
class ProfilRenderer {
    /**
     * Génère le HTML de la page profil d'un patient
     * @param string $email Email du patient
     * @return string Contenu de la page profil
     */
    public static function render($id, $email, $name, $phone, $address, $nbC, $nbF): string {
        $consultations = PatientModel::getConsultationsByPatientId($id);
        $invoices = PatientModel::getInvoicesByPatientId($id);
        $unbilledConsultations = PatientModel::getUnbilledConsultationsByPatientId($id);
        $nbUnbilled = count($unbilledConsultations);
    
        $editPatientModal = EditPatientRenderer::render($id, $name, $email, $phone, $address);
        $removePatientModal = RemovePatientRenderer::render($id, $name, $email, $phone, $address);
        $addConsultationModal = AddConsultationRenderer::render($id);
        $selectConsultModal = SelectConsultRenderer::render($id);

        // Bloc consultations : table complète seulement s'il y a des lignes
        if ($consultations !== "") {
            $consultationsBlock = <<<HTML
                        <div class="profil-list-card">
                            <h3>Consultations</h3>
                            <table>
                                <tr>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Facturée</th>
                                    <th>Action</th>
                                </tr>
                                $consultations
                            </table>
                        </div>
            HTML;
        } else {
            $consultationsBlock = <<<HTML
                        <div class="profil-list-card">
                            <h3>Consultations</h3>
                            <p class="profil-list-empty">Aucune consultation enregistrée pour le moment.</p>
                        </div>
            HTML;
        }

        // Bloc factures : table complète seulement s'il y a des lignes
        if ($invoices !== "") {
            $invoicesBlock = <<<HTML
                        <div class="profil-list-card">
                            <h3>Factures</h3>
                            <table>
                                <tr>
                                    <th>Numéro</th>
                                    <th>Montant Total</th>
                                    <th>Date</th>
                                    <th>Nb Consultations</th>
                                    <th>Action</th>
                                </tr>
                                $invoices
                            </table>
                        </div>
            HTML;
        } else {
            $invoicesBlock = <<<HTML
                        <div class="profil-list-card">
                            <h3>Factures</h3>
                            <p class="profil-list-empty">Aucune facture disponible pour ce patient.</p>
                        </div>
            HTML;
        }

        return <<<HTML
            <div class="page-shell">
                <div class="profil-container">
                    <header class="profil-header">
                        <div>
                            <h2>Profil du patient</h2>
                            <p class="profil-subtitle">Synthèse des informations et historique de suivi</p>
                        </div>
                        <span class="badge">
                            <span class="badge-dot"></span>
                            Patient
                        </span>
                    </header>
                    
                    <section class="profil-info">
                        <p><strong>Id:</strong> $id</p>
                        <p><strong>Email:</strong> $email</p>
                        <p><strong>Nom - Prénom:</strong> $name</p>
                        <p><strong>Téléphone:</strong> $phone</p>
                        <p><strong>Adresse:</strong> $address</p>
                        <p><strong>Nb consultations:</strong> $nbC</p>
                        <p><strong>Nb factures:</strong> $nbF</p>
                        <p><strong>Consultations non facturées:</strong> $nbUnbilled</p>
                    </section>

                    <section class="profil-actions">
                        $editPatientModal
                        $removePatientModal
                        $addConsultationModal
                        $selectConsultModal
                    </section>

                    <section class="profil-lists" style="display:flex;flex-direction:column;gap:1rem;">
                        $consultationsBlock

                        $invoicesBlock
                    </section>
                    
                    <a href="?action=dashboard" class="back-link">Retour à la liste des patients</a>
                </div>
            </div>
        HTML;
    }

    public static function renderConsultation($id, $name, $date, $isBilled, $patientId):string{
        $billedText = $isBilled ? 'Oui' : 'Non';
        $deleteButton = '';
        if (!$isBilled) {
            $deleteButton = '<a href="?action=delete-consultation&id=' . $id . '&patient=' . $patientId . '" class="button button-danger button-small" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cette consultation ?\');">🗑 Supprimer</a>';
        } else {
            $deleteButton = '<span class="text-soft">—</span>';
        }
        return <<<HTML
            <tr>
                <td>$name</td>
                <td>$name</td>
                <td>$date</td>
                <td>$billedText</td>
                <td>$deleteButton</td>
            </tr>
        HTML;
    }

    public static function renderInvoice($id, $totalAmount, $createdAt, $nbConsultations, $patientId):string{
        return <<<HTML
            <tr>
                <td>$id</td>
                <td>$totalAmount €</td>
                <td>$createdAt</td>
                <td>$nbConsultations</td>
                <td>
                    <div class="action-container">
                        <a href="?action=download-invoice&id=$id" class="button button-secondary" title="Télécharger la facture">
                            ⬇ Télécharger
                        </a>
                        <a href="?action=resend-invoice&id=$id&patient=$patientId" class="button button-primary" title="Renvoyer la facture par mail">
                            ✉ Renvoyer
                        </a>
                    </div>
                </td>
            </tr>
        HTML;
    }
}
