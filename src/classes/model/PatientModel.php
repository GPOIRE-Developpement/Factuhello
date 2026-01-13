<?php

namespace guillaumepaquin\factuhello\model;

use guillaumepaquin\factuhello\model\Repository;
use guillaumepaquin\factuhello\render\SuccessRenderer;
use guillaumepaquin\factuhello\render\ErrorRenderer;
use guillaumepaquin\factuhello\render\DashboardRenderer;

class PatientModel {
    public static function getPatients(){
        $patients = "";

        $pdo = Repository::getInstance()->getPdo();
        try{
            $stmt = $pdo->prepare("SELECT p.id, p.email, p.name, p.phone, p.address,
                (SELECT COUNT(*) FROM consultations c WHERE c.patient_id = p.id) nb_consultations,
                (SELECT COUNT(DISTINCT ic.invoice_id) 
                 FROM consultations c 
                 INNER JOIN invoice_consultations ic ON c.id = ic.consultation_id 
                 WHERE c.patient_id = p.id) nb_invoices
                FROM patients p");
            $stmt->execute();

            if($stmt->rowCount() == 0){
                return "";
            }

            while($row = $stmt->fetch()){
                $patients .= DashboardRenderer::renderPatient(
                    $row['id'],
                    $row['email'],
                    $row['name'],
                    $row['phone'],
                    $row['address'],
                    $row['nb_consultations'],
                    $row['nb_invoices']
                );
            }

            return $patients;
        }catch(\PDOException $e){
            return "";
        }
    }

    public static function addPatient($name, $email, $phone, $address):string {
        $pdo = Repository::getInstance()->getPdo();

        try{
            $stmt = $pdo->prepare("INSERT INTO patients (name, email, phone, address) VALUES (:name, :email, :phone, :address)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':address' => $address
            ]);

            return SuccessRenderer::render("Patient ajouté avec succès.", "?action=dashboard");
        }catch(\PDOException $e){
            return ErrorRenderer::render("Erreur lors de l'ajout du patient : erreur de base de données.", "?action=dashboard");
        }
    }

    public static function getPatientById($patientId){
        $pdo = Repository::getInstance()->getPdo();

        try {
            $stmt = $pdo->prepare("SELECT p.id, p.email, p.name, p.phone, p.address,
                (SELECT COUNT(*) FROM consultations c WHERE c.patient_id = p.id) nb_consultations,
                (SELECT COUNT(DISTINCT ic.invoice_id) 
                 FROM consultations c 
                 INNER JOIN invoice_consultations ic ON c.id = ic.consultation_id 
                 WHERE c.patient_id = p.id) nb_invoices
                FROM patients p 
                WHERE p.id = :id");
            $stmt->execute([':id' => $patientId]);

            if ($stmt->rowCount() == 0) {
                return null;
            }

            return $stmt->fetch();
        } catch (\PDOException $e) {
            return null;
        }
    }
}