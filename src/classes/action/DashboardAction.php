<?php

namespace guillaumepaquin\factuhello\action;

use guillaumepaquin\factuhello\model\AccountModel;
use guillaumepaquin\factuhello\render\DashboardRenderer;
use guillaumepaquin\factuhello\model\PatientModel;

/**
 * Action par défaut : page de tableau de bord
 */
class DashboardAction extends Action {
    /**
     * Execute lorsqu'il n'y a pas d'action spécifique demandée, retourne la page de tableau de bord
     * @return string
     */
    public static function execute(): string {
        if(!AccountModel::isLoggedIn()){
            header("Location: ?action=login");
            exit();
        }

        $patients = PatientModel::getPatients();

        return DashboardRenderer::render($patients);    
    }
}