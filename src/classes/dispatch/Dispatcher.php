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
use guillaumepaquin\factuhello\action\RemovePatientAction;
use guillaumepaquin\factuhello\action\AddConsultationAction;
use guillaumepaquin\factuhello\action\GenerateInvoiceAction;
use guillaumepaquin\factuhello\action\DownloadPdfAction;
use guillaumepaquin\factuhello\action\DownloadInvoiceAction;
use guillaumepaquin\factuhello\action\ResendInvoiceAction;
use guillaumepaquin\factuhello\action\DeleteConsultationAction;
use guillaumepaquin\factuhello\action\AdminAction;
use guillaumepaquin\factuhello\action\LogoutAction;

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
            "remove-patient" => RemovePatientAction::class,
            "add-consultation" => AddConsultationAction::class,
            "generate-invoice" => GenerateInvoiceAction::class,
            "download-pdf" => DownloadPdfAction::class,
            "download-invoice" => DownloadInvoiceAction::class,
            "resend-invoice" => ResendInvoiceAction::class,
            "delete-consultation" => DeleteConsultationAction::class,
            "admin" => AdminAction::class,
            "logout" => LogoutAction::class
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
            
            // Pour le téléchargement PDF, ne pas encapsuler dans la page
            if ($action === 'download-pdf' || $action === 'download-invoice') {
                $objA::execute();
                return;
            }
            
            $this->renderPage($objA::execute(), $action);
        } else {
            $this->renderPage((new DefaultAction())->execute(), 'default');
        }
    }

    /**
     * Construit et affiche la page complète
     * @param string $html Contenu HTML à afficher
     */
    private function renderPage($html, string $action): void{
        $title = 'Factuhello';
        $bodyClass = '';

        switch ($action) {
            case 'login':
                $title = 'Factuhello - Connexion';
                break;
            case 'register':
                $title = 'Factuhello - Inscription';
                break;
            case 'forgot':
                $title = 'Factuhello - Mot de passe oublié';
                break;
            case 'reset':
                $title = 'Factuhello - Réinitialisation';
                break;
            case 'dashboard':
                $title = 'Factuhello - Tableau de bord';
                $bodyClass = 'dashboard-page';
                break;
            case 'profil':
                $title = 'Factuhello - Profil Patient';
                $bodyClass = 'dashboard-page';
                break;
            case 'admin':
                $title = 'Factuhello - Administration';
                $bodyClass = 'dashboard-page';
                break;
            default:
                $title = 'Factuhello';
                break;
        }

        echo PageRenderer::render($html, $title, $bodyClass);
    }
}