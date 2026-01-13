<?php

namespace guillaumepaquin\factuhello\render;

/**
 * Classe responsable du rendu de la page profil
 */
class ProfilRenderer {
    /**
     * Génère le HTML de la page profil d'un patient
     * @param string $email Email du patient
     * @return string Contenu de la page profil
     */
    public static function render(string $id): string {
        return <<<HTML
            <div class="profil-container">
                <h2>Profil du patient</h2>
                
                <div class="profil-info">
                    <p><strong>Email:</strong>gpoire.developpement@gmail.com</p>
                    <p><strong>Nom - Prénom:</strong> Paquin - Guillaume</p>
                    <p><strong>Téléphone:</strong> 06 52 34 87 90</p>
                    <p><strong>Adresse:</strong> 10 rue des Lilas, 75000 Paris</p>
                    <p><strong>Nb consultations:</strong> 5</p>
                    <p><strong>Nb factures:</strong> 3</p>
                </div>
                
                <div class="profil-actions">
                    <button onclick="alert('Modifier les informations')">Modifier les informations</button>
                    <button onclick="alert('Supprimer le patient')">Supprimer le patient</button>
                    <button onclick="alert('Ajouter une séance')">Ajouter une séance</button>
                    <button onclick="alert('Générer une facture')">Générer une facture</button>
                </div>
                
                <a href="?action=dashboard" class="back-link">← Retour à la liste des patients</a>
            </div>
        HTML;
    }
}
