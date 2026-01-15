<?php

namespace guillaumepaquin\factuhello\render;

use guillaumepaquin\factuhello\model\AccountModel;

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

        // Bouton admin seulement pour les administrateurs
        $adminButton = '';
        if (AccountModel::isAdmin()) {
            $adminButton = '<a href="?action=admin" class="button button-secondary">⚙ Administration</a>';
        }

        return <<<HTML
<div class="page-shell">
    <header class="page-header">
        <div>
            <h1 class="page-header-title">Tableau de bord</h1>
            <p class="page-header-subtitle">Vue d'ensemble de vos patients et prestations</p>
        </div>
        <div class="board-header-actions">
            <button type="button" class="button button-primary" onclick="modalPatient.showModal()">Ajouter un patient</button>
            <button type="button" class="button button-secondary" onclick="modalBenefit.showModal()">Ajouter une prestation</button>
            $adminButton
            <a href="?action=logout" class="button button-ghost">Déconnexion</a>
        </div>
    </header>

    <main class="layout-board">
        <section class="board-main-card">
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
        </section>
    </main>
</div>

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
