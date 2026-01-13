<?php

namespace guillaumepaquin\factuhello\action;

use guillaumepaquin\factuhello\model\AccountModel;
use guillaumepaquin\factuhello\render\ErrorRenderer;
use guillaumepaquin\factuhello\model\BenefitModel;

/**
 * Action par défaut : page de tableau de bord
 */
class AddBenefitAction extends Action {
    /**
     * Execute lorsqu'il n'y a pas d'action spécifique demandée, retourne la page d'ajout d'une préstation
     * @return string
     */
    public static function execute(): string {
        if(!AccountModel::isLoggedIn()){
            header("Location: ?action=login");
            exit();
        }

        $name = $_POST['name'] ?? null;
        $description = $_POST['description'] ?? null;
        $price = $_POST['price'] ?? null;

        if(!isset($name) || !isset($description) || !isset($price)){
            return ErrorRenderer::render("Tous les champs sont obligatoires.", "?action=dashboard");
        }

        return BenefitModel::addBenefit($name, $description, $price);
    }
}