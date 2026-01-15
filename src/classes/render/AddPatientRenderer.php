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
<dialog id="modalPatient">
    <p>Ajouter un nouveau patient</p>

    <div class="nom">
        <label for="patient-name">Nom :</label>
        <input type="text" id="patient-name" name="patient-name" required>
    </div>

    <div class="email">
        <label for="patient-email">Email :</label>
        <input type="email" id="patient-email" name="patient-email" required>
    </div>

    <div class="phone">
        <label for="patient-phone">Téléphone :</label>
        <input type="phone" id="patient-phone" name="patient-phone" required>
    </div>

    <div class="address">
        <label for="patient-address">Adresse :</label>
        <input type="text" id="patient-address" name="patient-address" required>
    </div>

    <button onclick="submitNewPatient()" class="button button-primary">Ajouter le patient</button>
    <button onclick="modalPatient.close()" class="button button-ghost">Fermer</button>
</dialog>

<script>
    async function submitNewPatient(){
        const data = new FormData();
        data.append("name", document.getElementById('patient-name').value);
        data.append("email", document.getElementById('patient-email').value);
        data.append("phone", document.getElementById('patient-phone').value);
        data.append("address", document.getElementById('patient-address').value);

        const response = await fetch("?action=add-patient", {
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