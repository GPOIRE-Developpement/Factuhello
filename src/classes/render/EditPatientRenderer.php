<?php

namespace guillaumepaquin\factuhello\render;

/**
 * Classe responsable du rendu des pages
 */
class EditPatientRenderer {
    /**
     * Génère le HTML de la page de modification d'un patient
     * @return string Contenu de la page de modification d'un patient
     */
    public static function render($id, $name, $email, $phone, $address): string {
        return <<<HTML
            <button onclick="modalEditPatient.showModal()">Modifier le patient</button>

            <dialog id="modalEditPatient">
                <p>Ajouter un nouveau patient</p>

                <input type="hidden" id="patient-id" name="patient-id" value="{$id}">

                <div class="nom">
                    <label for="patient-name">Nom :</label>
                    <input type="text" id="patient-name" name="patient-name" value="{$name}" required>
                </div>

                <div class="email">
                    <label for="patient-email">Email :</label>
                    <input type="email" id="patient-email" name="patient-email" value="{$email}" required>
                </div>

                <div class="phone">
                    <label for="patient-phone">Téléphone :</label>
                    <input type="phone" id="patient-phone" name="patient-phone" value="{$phone}" required>
                </div>

                <div class="address">
                    <label for="patient-address">Addresse :</label>
                    <input type="text" id="patient-address" name="patient-address" value="{$address}" required>
                </div>


                <button onclick="submitEditPatient()">Modifier le patient</button>
                <button onclick="modalEditPatient.close()">Fermer</button>
            </dialog>

            <script>
                async function submitEditPatient(){
                    const data = new FormData();
                    data.append("id", document.getElementById('patient-id').value);
                    data.append("name", document.getElementById('patient-name').value);
                    data.append("email", document.getElementById('patient-email').value);
                    data.append("phone", document.getElementById('patient-phone').value);
                    data.append("address", document.getElementById('patient-address').value);

                    const response = await fetch("?action=edit-patient", {
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