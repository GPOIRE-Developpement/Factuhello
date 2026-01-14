<?php

namespace guillaumepaquin\factuhello\model;

use guillaumepaquin\factuhello\model\Repository;
use guillaumepaquin\factuhello\render\SuccessRenderer;
use guillaumepaquin\factuhello\render\ErrorRenderer;
use guillaumepaquin\factuhello\render\DashboardRenderer;
use guillaumepaquin\factuhello\render\ProfilRenderer;

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

    public static function editPatient($id, $name, $email, $phone, $address):string {
        $pdo = Repository::getInstance()->getPdo();

        $url = "?action=dashboard";
        if(isset($id)){
            $url = "?action=profil&id=" . urlencode($id);
        }

        try{
            $stmt = $pdo->prepare("UPDATE patients SET name = :name, email = :email, phone = :phone, address = :address WHERE id = :id");
            $stmt->execute([
                ':id' => $id,
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':address' => $address
            ]);

            return SuccessRenderer::render("Patient modifié avec succès.", $url);
        }catch(\PDOException $e){
            return ErrorRenderer::render("Erreur lors de la modification du patient : erreur de base de données.", $url);
        }
    }

    public static function addConsultation($patientId, $date, $benefitId): string{
        $pdo = Repository::getInstance()->getPdo();

        $url = "?action=dashboard";
        if(isset($patientId)){
            $url = "?action=profil&id=" . urlencode($patientId);
        }

        try{
            $stmt = $pdo->prepare("INSERT INTO consultations (patient_id, date, benefit_id) VALUES (:patient_id, :date, :benefit_id)");
            $stmt->execute([
                ':patient_id' => $patientId,
                ':date' => $date,
                ':benefit_id' => $benefitId
            ]);

            return SuccessRenderer::render("Consultation ajoutée avec succès.", $url);
        }catch(\PDOException $e){
            return ErrorRenderer::render("Erreur lors de l'ajout de la consultation : erreur de base de données.", $url);
        }
    }

    public static function getConsultationsByPatientId($patientId): string {
        $pdo = Repository::getInstance()->getPdo();
        $consultations = "";

        try{
            $stmt = $pdo->prepare("SELECT c.id, c.date, b.name as benefit_name, b.price as benefit_price
                FROM consultations c
                INNER JOIN benefits b ON c.benefit_id = b.id
                WHERE c.patient_id = :patient_id
                ORDER BY c.date DESC");
            $stmt->execute([
                ':patient_id' => $patientId
            ]);

            while($row = $stmt->fetch()){
                $consultations .= ProfilRenderer::renderConsultation(
                    $row['id'],
                    $row['benefit_name'],
                    $row['date'],
                    $row['benefit_name']
                );
            }

            return $consultations;
        }catch(\PDOException $e){
            return [];
        }
    }

    public static function getInvoicesByPatientId($patientId): string {
        $pdo = Repository::getInstance()->getPdo();
        $invoices = "";

        try{
            $stmt = $pdo->prepare("SELECT i.id, i.total_amount, i.created_at, COUNT(ic.consultation_id) as nb_consultations
                FROM invoices i
                INNER JOIN invoice_consultations ic ON i.id = ic.invoice_id
                INNER JOIN consultations c ON ic.consultation_id = c.id
                WHERE c.patient_id = :patient_id
                GROUP BY i.id, i.total_amount, i.created_at
                ORDER BY i.created_at DESC");
            $stmt->execute([
                ':patient_id' => $patientId
            ]);

            while($row = $stmt->fetch()){
                $invoices .= ProfilRenderer::renderInvoice(
                    $row['id'],
                    $row['total_amount'],
                    $row['created_at'],
                    $row['nb_consultations']
                );
            }

            return $invoices;
        }catch(\PDOException $e){
            return "";
        }
    }

    public static function getUnbilledConsultationsByPatientId($patientId): array {
        $pdo = Repository::getInstance()->getPdo();
        $consultations = [];

        try{
            $stmt = $pdo->prepare("SELECT c.id, c.date, b.name as benefit_name, b.price as benefit_price
                FROM consultations c
                INNER JOIN benefits b ON c.benefit_id = b.id
                WHERE c.patient_id = :patient_id AND c.id NOT IN (SELECT consultation_id FROM invoice_consultations)
                ORDER BY c.date DESC");
            $stmt->execute([
                ':patient_id' => $patientId
            ]);

            while($row = $stmt->fetch()){
                $consultations[] = $row;
            }

            return $consultations;
        }catch(\PDOException $e){
            return [];
        }
    }

    public static function removePatient($id):string {
        $pdo = Repository::getInstance()->getPdo();

        $url = "?action=dashboard";
        if(isset($id)){
            $url = "?action=profil&id=" . urlencode($id);
        }

        try{
            $stmt = $pdo->prepare("SELECT COUNT(*) as nb_unbilled FROM consultations WHERE patient_id = :id AND id NOT IN (SELECT consultation_id FROM invoice_consultations)");
            $stmt->execute([
                ':id' => $id
            ]);

            $row = $stmt->fetch();
            if($row['nb_unbilled'] > 0){
                return ErrorRenderer::render("Impossible de supprimer le patient : il a des prestations non facturées.", $url);
            }

            $stmt = $pdo->prepare("DELETE FROM patients WHERE id = :id");
            $stmt->execute([
                ':id' => $id
            ]);

            $stmt = $pdo->prepare("DELETE FROM consultations WHERE patient_id = :id");
            $stmt->execute([
                ':id' => $id
            ]);

            $stmt = $pdo->prepare("DELETE ic FROM invoice_consultations ic INNER JOIN
                    consultations c ON ic.consultation_id = c.id WHERE c.patient_id = :id");
            $stmt->execute([
                ':id' => $id
            ]);

            return SuccessRenderer::render("Patient supprimé avec succès.", "?action=dashboard");
        }catch(\PDOException $e){
            return ErrorRenderer::render("Erreur lors de la suppression du patient : erreur de base de données.", $url);
        }
    }
}