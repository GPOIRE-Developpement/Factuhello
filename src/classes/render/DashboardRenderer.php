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
    public static function render(): string {
        $patient = AddPatientRenderer::render();
        $benefit = AddBenefitRenderer::render();

        return <<<HTML
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
                <tr class="clickable-row" onclick="window.location='?action=profil&id=1'">
                    <td>gpoire.developpement@gmail.com</td>
                    <td>Paquin - Guillaume</td>
                    <td>06 52 34 87 90</td>
                    <td>10 rue des Lilas, 75000 Paris</td>
                    <td>5</td>
                    <td>3</td>
                </tr>
                <tr class="clickable-row" onclick="window.location='?action=profil&email=gpoire.developpement@gmail.com'">
                    <td>gpoire.developpement@gmail.com</td>
                    <td>Paquin - Guillaume</td>
                    <td>06 52 34 87 90</td>
                    <td>10 rue des Lilas, 75000 Paris</td>
                    <td>5</td>
                    <td>3</td>
                </tr>
                <tr class="clickable-row" onclick="window.location='?action=profil&email=gpoire.developpement@gmail.com'">
                    <td>gpoire.developpement@gmail.com</td>
                    <td>Paquin - Guillaume</td>
                    <td>06 52 34 87 90</td>
                    <td>10 rue des Lilas, 75000 Paris</td>
                    <td>5</td>
                    <td>3</td>
                </tr>
                <tr class="clickable-row" onclick="window.location='?action=profil&email=gpoire.developpement@gmail.com'">
                    <td>gpoire.developpement@gmail.com</td>
                    <td>Paquin - Guillaume</td>
                    <td>06 52 34 87 90</td>
                    <td>10 rue des Lilas, 75000 Paris</td>
                    <td>5</td>
                    <td>3</td>
                </tr>
                <tr class="clickable-row" onclick="window.location='?action=profil&email=gpoire.developpement@gmail.com'">
                    <td>gpoire.developpement@gmail.com</td>
                    <td>Paquin - Guillaume</td>
                    <td>06 52 34 87 90</td>
                    <td>10 rue des Lilas, 75000 Paris</td>
                    <td>5</td>
                    <td>3</td>
                </tr>
                <tr class="clickable-row" onclick="window.location='?action=profil&email=gpoire.developpement@gmail.com'">
                    <td>gpoire.developpement@gmail.com</td>
                    <td>Paquin - Guillaume</td>
                    <td>06 52 34 87 90</td>
                    <td>10 rue des Lilas, 75000 Paris</td>
                    <td>5</td>
                    <td>3</td>
                </tr>
                <tr class="clickable-row" onclick="window.location='?action=profil&email=gpoire.developpement@gmail.com'">
                    <td>gpoire.developpement@gmail.com</td>
                    <td>Paquin - Guillaume</td>
                    <td>06 52 34 87 90</td>
                    <td>10 rue des Lilas, 75000 Paris</td>
                    <td>5</td>
                    <td>3</td>
                </tr>
            </table>
            
            $patient

            $benefit
        HTML;
    }
}