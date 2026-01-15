<?php

namespace guillaumepaquin\factuhello\render;

use guillaumepaquin\factuhello\model\BenefitModel;

/**
 * Classe responsable du rendu des pages
 */
class AddConsultationRenderer {
    /**
     * Génère le HTML de la page d'ajout d'une prestation
     * @return string Contenu de la page d'ajout d'une prestation
     */
    public static function render($id): string {
        $today = date('Y-m-d');
        $min = date('Y-m-d', strtotime($today . ' -1 year'));
        $max = date('Y-m-d', strtotime($today . ' +1 year'));
        $benefits = BenefitModel::getBenefits();
        $options = self::renderListOptions($benefits);

        return <<<HTML
<button type="button" class="button button-primary" onclick="modalAddConsultation.showModal()">Ajouter une séance</button>

<dialog id="modalAddConsultation">
	<div class="modal-invoice">
		<p class="modal-title">Ajouter une séance</p>
		<p class="modal-subtitle">Choisissez la date de la séance et la prestation associée.</p>

		<input type="hidden" id="consultation-patientid" name="patient-id" value="{$id}">

		<div class="field-group">
			<label for="consultation-date">Date de la séance</label>
			<input
				type="date"
				id="consultation-date"
				name="consultation-date"
				class="date-field"
				value="{$today}"
				min="{$min}"
				max="{$max}" />
		</div>
		<div class="field-group">
			<label for="consultation-type">Type de consultation</label>
			<select id="consultation-type" name="consultation-type" class="select-field">
				$options
			</select>
		</div>

		<div class="action-container modal-actions">
			<button onclick="submitNewBenefit()" class="button button-primary">Ajouter la séance</button>
			<button onclick="modalAddConsultation.close()" class="button button-ghost">Fermer</button>
		</div>
	</div>
</dialog>

<script>
	async function submitNewBenefit(){
		const data = new FormData();
		data.append("patient", document.getElementById('consultation-patientid').value);
		data.append("time", document.getElementById('consultation-date').value);
		data.append("benefit", document.getElementById('consultation-type').value);

		const response = await fetch("?action=add-consultation", {
			method: "POST",
			body: data
		});

		const html = await response.text();
		document.body.innerHTML = html;
	}
</script>
HTML;
    }

    public static function renderListOptions($benefits): string {
        $options = "";
        foreach($benefits as $benefit){
            $options .= "<option value=\"{$benefit['id']}\">{$benefit['name']}</option>";
        }
        return $options;
    }
}