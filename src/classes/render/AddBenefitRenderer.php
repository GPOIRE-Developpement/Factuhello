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
                    <label for="benefit-name">Nom :</label>
                    <input type="text" id="benefit-name" name="name" required>
                </div>

                <div class="description">
                    <label for="benefit-description">Description :</label>
                    <input type="text" id="benefit-description" name="description" required>
                </div>

                <div class="price">
                    <label for="benefit-price">Prix :</label>
                    <input type="number" id="benefit-price" name="price" required>
                </div>

                <button onclick="submitNewBenefit()">Ajouter la prestation</button>
                <button onclick="modalBenefit.close()">Fermer</button>
            </dialog>

            <script>
                async function submitNewBenefit(){
                    const data = new FormData();
                    data.append("name", document.getElementById('benefit-name').value);
                    data.append("description", document.getElementById('benefit-description').value);
                    data.append("price", document.getElementById('benefit-price').value);

                    const response = await fetch("?action=add-benefit", {
                        method: "POST",
                        body: data
                    });

                    const html = await response.text();
                    document.body.innerHTML = html;
                }
            </script>
        HTML;
    }
}