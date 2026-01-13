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
    public static function render($id, $email, $name, $phone, $address, $nbC, $nbF): string {
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
                    <button onclick="alert('Modifier les informations')">Modifier les informations</button>
                    <button onclick="alert('Supprimer le patient')">Supprimer le patient</button>
                    <button onclick="alert('Ajouter une séance')">Ajouter une séance</button>
                    <button onclick="alert('Générer une facture')">Générer une facture</button>
                </div>

                <p>Liste des consultations</p>

                <p>List des factures</p>
                
                <a href="?action=dashboard" class="back-link">← Retour à la liste des patients</a>
            </div>
        HTML;
    }
}
