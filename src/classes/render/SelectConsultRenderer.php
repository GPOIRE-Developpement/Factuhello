<?php

namespace guillaumepaquin\factuhello\render;

use guillaumepaquin\factuhello\model\PatientModel;

/**
 * Classe responsable du rendu du modal de selection des consultations non facturées
 */
class SelectConsultRenderer {
    /**
     * Génère le HTML du modal de selection des consultations à facturer
     * @param int $patientId ID du patient
     * @return string Contenu du modal de selection
     */
    public static function render($patientId): string {
        $consultations = PatientModel::getUnbilledConsultationsByPatientId($patientId);
        $consultationRows = self::renderConsultationRows($consultations);

        return <<<HTML
<button type="button" class="button button-primary" onclick="modalSelectConsult.showModal()">
    🧾 Générer une facture
</button>

<dialog id="modalSelectConsult">
    <div class="modal-invoice">
        <header class="modal-invoice-header">
            <div>
                <h3>Générer une facture</h3>
                <p class="modal-invoice-subtitle">Sélectionnez les séances à inclure et appliquez une éventuelle réduction.</p>
            </div>
        </header>

        <input type="hidden" id="patient-id" name="patient-id" value="$patientId">

        <section class="modal-invoice-body">
            <div class="modal-invoice-table">
                <table>
                    <tr>
                        <th></th>
                        <th>Date</th>
                        <th>Type de consultation</th>
                        <th>Prix</th>
                    </tr>
                    $consultationRows
                </table>
            </div>
            <div class="modal-invoice-summary">
                <div class="field-group">
                    <label for="reduction-percent">Réduction appliquée (%)</label>
                    <input type="number" id="reduction-percent" name="reduction-percent" value="0" min="0" max="100" onchange="calculateTotal()" oninput="calculateTotal()">
                </div>
                <div class="modal-invoice-totals">
                    <p>
                        <span>Montant brut :</span>
                        <span class="amount"><span id="total-price">0,00</span> €</span>
                    </p>
                    <p>
                        <span>Réduction :</span>
                        <span class="amount"><span id="reduction-amount">0,00</span> €</span>
                    </p>
                    <p>
                        <span>Total à facturer :</span>
                        <span class="amount amount-strong"><span id="final-price">0,00</span> €</span>
                    </p>
                </div>
            </div>
        </section>

        <footer class="modal-invoice-footer action-container">
            <button onclick="submitSelectConsultation()" class="button button-primary">Générer la facture</button>
            <button onclick="modalSelectConsult.close()" class="button button-ghost">Annuler</button>
        </footer>
    </div>
</dialog>

<script>
    function calculateTotal(){
        const checkboxes = document.querySelectorAll('input[name="consultation-checkbox"]:checked');
        const reductionPercent = parseFloat(document.getElementById('reduction-percent').value) || 0;

        let totalPrice = 0;
        checkboxes.forEach(cb => {
            const row = cb.closest('tr');
            const priceText = row.cells[3].textContent.replace(' €', '').replace(',', '.');
            totalPrice += parseFloat(priceText);
        });

        const reductionAmount = totalPrice * (reductionPercent / 100);
        const finalPrice = totalPrice - reductionAmount;

        document.getElementById('total-price').textContent = totalPrice.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('reduction-amount').textContent = reductionAmount.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('final-price').textContent = finalPrice.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    // Recalculer le total quand une checkbox change
    document.addEventListener('change', function(e) {
        if(e.target.name === 'consultation-checkbox') {
            calculateTotal();
        }
    });

    async function submitSelectConsultation(){
        const checkboxes = document.querySelectorAll('input[name="consultation-checkbox"]:checked');
        const consultationIds = Array.from(checkboxes).map(cb => cb.value);
        const reductionPercent = parseFloat(document.getElementById('reduction-percent').value) || 0;

        if(consultationIds.length === 0){
            alert('Veuillez sélectionner au moins une consultation');
            return;
        }

        const patientId = document.getElementById('patient-id').value;
        const data = new FormData();
        data.append("patient_id", patientId);
        data.append("consultation_ids", JSON.stringify(consultationIds));
        data.append("reduction_percent", reductionPercent);

        try {
            const response = await fetch("?action=generate-invoice", {
                method: "POST",
                body: data
            });

            const html = await response.text();
            document.body.innerHTML = html;
        } catch (error) {
            alert('Erreur lors de la génération de la facture');
            console.error(error);
        }
    }

    // Initialiser le calcul au chargement
    calculateTotal();
</script>
HTML;
    }

    /**
     * Génère les lignes du tableau de selection des consultations
     * @param array $consultations Tableau des consultations
     * @return string Lignes du tableau
     */
    private static function renderConsultationRows($consultations): string {
        if (empty($consultations)) {
            return <<<HTML
<tr>
    <td colspan="4" style="text-align: center;">Aucune consultation non facturée</td>
</tr>
HTML;
        }

        $rows = "";
        foreach($consultations as $consultation){
            $id = $consultation['id'];
            $date = $consultation['date'];
            $benefitName = $consultation['benefit_name'];
            $price = number_format($consultation['benefit_price'], 2, ',', ' ');

            $rows .= <<<HTML
<tr>
    <td><input type="checkbox" name="consultation-checkbox" value="$id"></td>
    <td>$date</td>
    <td>$benefitName</td>
    <td>$price €</td>
</tr>
HTML;
        }

        return $rows;
    }
}
