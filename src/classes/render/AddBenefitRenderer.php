<?php

namespace guillaumepaquin\factuhello\render;

/**
 * Classe responsable du rendu des pages
 */
class AddBenefitRenderer {
    /**
     * Génère le HTML de la page d'ajout d'une prestation
     * @return string Contenu de la page d'ajout d'une prestation
     */
    public static function render(): string {
        return <<<HTML
            <button onclick="modalBenefit.showModal()">Ajouter une prestation</button>

            <dialog id="modalBenefit">
                <p>Contenu du modal prestation</p>
                
                <div class="name">
                    <label for="name">Nom :</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="description">
                    <label for="description">Description :</label>
                    <input type="text" id="description" name="description" required>
                </div>

                <div class="price">
                    <label for="price">Prix :</label>
                    <input type="number" id="price" name="price" required>
                </div>

                <button onclick="submitNewBenefit()">Ajouter la prestation</button>
                <button onclick="modalBenefit.close()">Fermer</button>
            </dialog>
        HTML;
    }
}