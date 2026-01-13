<?php

namespace guillaumepaquin\factuhello\render;

/**
 * Classe responsable du rendu des pages
 */
class AddPatientRenderer {
    /**
     * Génère le HTML de la page d'ajout d'un patient
     * @return string Contenu de la page d'ajout d'un patient
     */
    public static function render(): string {
        return <<<HTML
            <button onclick="modalPatient.showModal()">Ajouter un patient</button>

            <dialog id="modalPatient">
                <p>Ajouter un nouveau patient</p>

                <div class="nom">
                    <label for="name">Nom :</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="email">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="phone">
                    <label for="phone">Téléphone :</label>
                    <input type="phone" id="phone" name="phone" required>
                </div>

                <div class="address">
                    <label for="address">Addresse :</label>
                    <input type="text" id="address" name="address" required>
                </div>


                <button onclick="submitNewPatient()">Ajouter le patient</button>
                <button onclick="modalPatient.close()">Fermer</button>
            </dialog>
        HTML;
    }
}