<?php

namespace guillaumepaquin\factuhello\render;

/**
 * Classe responsable du rendu des pages
 */
class RemovePatientRenderer {
    /**
     * Génère le HTML de la page de suppression d'un patient
     * @return string Contenu de la page de suppression d'un patient
     */
    public static function render($id, $name, $email, $phone, $address): string {
        return <<<HTML
<button type="button" class="button button-danger" onclick="modalPatient.showModal()">Supprimer le patient</button>

<dialog id="modalPatient">
    <div class="modal-invoice">
        <p class="modal-title">Supprimer le patient</p>
        <p class="modal-subtitle text-danger">La suppression d'un patient est définitive. Elle entraînera la suppression de l'ensemble de ses données.</p>

        <input type="hidden" id="patient-id" name="patient-id" value="{$id}">

        <div class="nom">
            <label for="patient-name">Nom :</label>
            <input type="text" id="patient-name" name="patient-name" value="{$name}" readonly>
        </div>

        <div class="email">
            <label for="patient-email">Email :</label>
            <input type="email" id="patient-email" name="patient-email" value="{$email}" readonly>
        </div>

        <div class="phone">
            <label for="patient-phone">Téléphone :</label>
            <input type="phone" id="patient-phone" name="patient-phone" value="{$phone}" readonly>
        </div>

        <div class="address">
            <label for="patient-address">Addresse :</label>
            <input type="text" id="patient-address" name="patient-address" value="{$address}" readonly>
        </div>

        <div class="action-container" style="margin-top: 1.4rem; justify-content: flex-end;">
            <button onclick="submitRemovePatient()" class="button button-danger">Supprimer le patient</button>
            <button onclick="modalPatient.close()" class="button button-ghost">Fermer</button>
        </div>
    </div>
</dialog>

<script>
	async function submitRemovePatient(){
		const data = new FormData();
		data.append("id", document.getElementById('patient-id').value);

		const response = await fetch("?action=remove-patient", {
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