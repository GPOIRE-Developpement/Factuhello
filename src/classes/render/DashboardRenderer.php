<?php

namespace guillaumepaquin\factuhello\render;

/**
 * Classe responsable du rendu des pages
 */
class DashboardRenderer {
    /**
     * Génère le HTML de la page principale du tableau de bord
     * @return string Contenu de la page principale du tableau de bord
     */
    public static function render($patients): string {
        $patientModal = AddPatientRenderer::render();
        $benefitModal = AddBenefitRenderer::render();

        return <<<HTML
            <div>
                <a href="?action=logout">🚪 Déconnexion</a>
            </div>
            <p>Liste des patients</p>
            <table>
                <tr>
                    <th>Email</th>
                    <th>Nom - Prénom</th>
                    <th>Téléphone</th>
                    <th>Adresse</th>
                    <th>Nb consultations</th>
                    <th>Nb factures</th>
                </tr>
                $patients
            </table>
            
            $patientModal

            $benefitModal
        HTML;
    }

    public static function renderPatient($id, $email, $name, $phone, $address, $nbC, $nbF):string{
        return <<<HTML
            <tr class="clickable-row" onclick="window.location='?action=profil&id={$id}'">
                <td>$email</td>
                <td>$name</td>
                <td>$phone</td>
                <td>$address</td>
                <td>$nbC</td>
                <td>$nbF</td>
            </tr>
        HTML;
    }
}