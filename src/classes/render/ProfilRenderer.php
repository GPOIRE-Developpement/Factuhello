<?php

namespace guillaumepaquin\factuhello\render;

use guillaumepaquin\factuhello\model\PatientModel;
use guillaumepaquin\factuhello\render\EditPatientRenderer;
use guillaumepaquin\factuhello\render\RemovePatientRenderer;
use guillaumepaquin\factuhello\render\AddConsultationRenderer;

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
    
        $editPatientModal = EditPatientRenderer::render($id, $name, $email, $phone, $address);
        $removePatientModal = RemovePatientRenderer::render($id, $name, $email, $phone, $address);
        $addConsultationModal = AddConsultationRenderer::render($id);

        return <<<HTML
            <div class="profil-container">
                <h2>Profil du patient</h2>
                
                <div class="profil-info">
                    <p><strong>Id:</strong>$id</p>
                    <p><strong>Email:</strong>$email</p>
                    <p><strong>Nom - Prénom:</strong>$name</p>
                    <p><strong>Téléphone:</strong>$phone</p>
                    <p><strong>Adresse:</strong>$address</p>
                    <p><strong>Nb consultations:</strong>$nbC</p>
                    <p><strong>Nb factures:</strong>$nbF</p>
                </div>

                <div class="profil-actions">
                    $editPatientModal
                    $removePatientModal
                    $addConsultationModal
                    <button onclick="alert('Générer une facture')">Générer une facture</button>
                </div>

                <p>Liste des consultations</p>
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

                <p>List des factures</p>
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
                
                <a href="?action=dashboard" class="back-link">← Retour à la liste des patients</a>
            </div>
        HTML;
    }

    public static function renderConsultation($id, $name, $time, $benefitName):string{
        return <<<HTML
            <tr>
                <td>$name</td>
                <td>$time</td>
                <td>Oui</td>
                <td>$benefitName</td>
            </tr>
        HTML;
    }

    public static function renderInvoice($id, $totalAmount, $createdAt, $nbConsultations):string{
        return <<<HTML
            <tr>
                <td>$id</td>
                <td>$totalAmount €</td>
                <td>$createdAt</td>
                <td>$nbConsultations</td>
                <td><button onclick="alert('Télécharger facture #$id')">Télécharger</button></td>
            </tr>
        HTML;
    }
}
