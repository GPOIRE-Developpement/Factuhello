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
                    <p><strong>Consultations non facturées:</strong> $nbUnbilled</p>
                </div>

                <div class="profil-actions">
                    $editPatientModal
                    $removePatientModal
                    $addConsultationModal
                    $selectConsultModal
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

    public static function renderInvoice($id, $totalAmount, $createdAt, $nbConsultations, $patientId):string{
        return <<<HTML
            <tr>
                <td>$id</td>
                <td>$totalAmount €</td>
                <td>$createdAt</td>
                <td>$nbConsultations</td>
                <td>
                    <a href="?action=download-invoice&id=$id">Télécharger</a>
                    <a href="?action=resend-invoice&id=$id&patient=$patientId">Renvoyer par mail</a>
                </td>
            </tr>
        HTML;
    }
}
