<?php
namespace guillaumepaquin\factuhello\dispatch;

use guillaumepaquin\factuhello\action\DefaultAction;
use guillaumepaquin\factuhello\render\EditPatientRenderer;
use guillaumepaquin\factuhello\render\PageRenderer;
use guillaumepaquin\factuhello\action\LoginAction;
use guillaumepaquin\factuhello\action\RegisterAction;
use guillaumepaquin\factuhello\action\ForgotAction;
use guillaumepaquin\factuhello\action\ResetAction;
use guillaumepaquin\factuhello\action\DashboardAction;
use guillaumepaquin\factuhello\action\ProfilAction;
use guillaumepaquin\factuhello\action\AddBenefitAction;
use guillaumepaquin\factuhello\action\AddPatientAction;
use guillaumepaquin\factuhello\action\EditPatientAction;

/**
 * Classe de routage principal de l'application
 * Associe les actions demandées aux classes correspondantes
 */
class Dispatcher{
    /**
     * Lance l'exécution de l'action demandée
     */
    public function run() {
        $action = $_GET['action'] ?? ($this->action ?? 'default');

        $actions = array(
            "default" => DefaultAction::class,
            "login" => LoginAction::class,
            "register" => RegisterAction::class,
            "forgot" => ForgotAction::class,
            "reset" => ResetAction::class,
            "dashboard" => DashboardAction::class,
            "profil" => ProfilAction::class,
            "add-benefit" => AddBenefitAction::class,
            "add-patient" => AddPatientAction::class,
            "edit-patient" => EditPatientAction::class,
        );

        // Dashboard
            // Voir la liste des patients
            // Ajouter un patient (modal)
            // Ajouter une prestation (modal)

        // Patient (en cliquant dessus dans la liste)
            // Voir les informations du patient
            // Modifier ses informations (modal)
            // Supprimer le patient (modal)

            // Ajouter une scéance au patient (bouton)
                // Voir la liste des scéances (modal)
                // Ajouter une scéance (modal)
            
            // Générer sa facture (envoyé par mail et imprimée) (bouton)

            // Revenir en arrière (liste des patients)
        
        
        if (isset($actions[$action])) {
            $a = $actions[$action];
            $objA = new $a();
            $this->renderPage($objA::execute());
        } else {
            $this->renderPage((new DefaultAction())->execute());
        }
    }

    /**
     * Construit et affiche la page complète
     * @param string $html Contenu HTML à afficher
     */
    private function renderPage($html): void{
        echo PageRenderer::render($html);
    }
}