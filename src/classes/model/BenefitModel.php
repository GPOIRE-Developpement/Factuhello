<?php

namespace guillaumepaquin\factuhello\model;

use guillaumepaquin\factuhello\model\Repository;
use guillaumepaquin\factuhello\render\SuccessRenderer;
use guillaumepaquin\factuhello\render\ErrorRenderer;

class BenefitModel {
    // Méthode pour ajouter une prestation
    public static function addBenefit($name, $description, $price):string {
        $pdo = Repository::getInstance()->getPdo();

        try{
            $stmt = $pdo->prepare("INSERT INTO benefits (name, description, price) VALUES (:name, :description, :price)");
            $stmt->execute([
                ':name' => $name,
                ':description' => $description,
                ':price' => $price
            ]);

            return SuccessRenderer::render("Prestation ajoutée avec succès.", "?action=dashboard");
        }catch(\PDOException $e){
            return ErrorRenderer::render("Erreur lors de l'ajout de la prestation : erreur de base de données.", "?action=dashboard");
        }
    }

    // Méthode pour supprimer une prestation

    // Récupérer l'ensemble des prestations

}