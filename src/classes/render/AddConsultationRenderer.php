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
            <button onclick="modalAddConsultation.showModal()">Ajouter d'une consultation</button>

            <dialog id="modalAddConsultation">
                <p>Contenu du modal prestation</p>
                
                <input type="hidden" id="consultation-patientid" name="patient-id" value="{$id}">

                <div class="time">
                    <label for="consultation-date">Date :</label>
                    <input
                    type="date"
                    id="consultation-date"
                    name="consultation-date"
                    value={$today}
                    min={$min}
                    max={$max} />
                </div>

                <div class="type">
                    <label for="consultation-type">Type de consultation</label>
                    <select id="consultation-type" name="consultation-type">
                        $options
                    </select>
                </div>

                <button onclick="submitNewBenefit()">Ajouter la prestation</button>
                <button onclick="modalAddConsultation.close()">Fermer</button>
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