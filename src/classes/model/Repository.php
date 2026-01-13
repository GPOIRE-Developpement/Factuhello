<?php

namespace guillaumepaquin\factuhello\model;

use PDO;

/**
 * Singleton pour la gestion de la connexion à la base de données
 */
class Repository {
    protected static ?array $config = null;
    protected static ?Repository $instance = null;
    protected ?PDO $pdo = null;

    /**
     * Charge la configuration depuis un fichier INI
     */
    public static function setConfig(string $file): void {
        self::$config = parse_ini_file($file);
    }

    /**
     * Retourne l'instance unique du repository (pattern Singleton)
     */
    public static function getInstance(): Repository {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialise la connexion PDO à la base de données
     */
    protected function __construct(){
        if (!self::$config) {
            throw new \Exception("Configuration non définie. Appeler setConfig() d'abord.");
        }
        
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8',
            self::$config['host'],
            self::$config['database']
        );
        
        $this->pdo = new PDO($dsn, self::$config['user'], self::$config['password']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Créer les tables si elles n'existent pas
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(190) UNIQUE NOT NULL,
                password_hash VARCHAR(190) NOT NULL,
                reset_token VARCHAR(190),
                reset_token_expiry DATETIME
            );
        ");

        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS patients (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(190) NOT NULL,
                email VARCHAR(190) UNIQUE NOT NULL,
                phone VARCHAR(50),
                address TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");

        // Cette table stocke l'ensemble des prestations proposées
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS benefits (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(190) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) NOT NULL
            );
        ");

        // Cette table stocke les prestations réalisé pour un patient
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS consultations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                patient_id INT NOT NULL,
                benefit_id INT NOT NULL,
                date DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (patient_id) REFERENCES patients(id),
                FOREIGN KEY (benefit_id) REFERENCES benefits(id)
            );
        ");

        // Cette table stocke le récapitulatif des factures générées
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS invoices (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tav INT NOT NULL,
                total_amount DECIMAL(10,2) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");

        // Cette table fait le lien entre les consultations et les factures
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS invoice_consultations (
                invoice_id INT NOT NULL,
                consultation_id INT NOT NULL,
                PRIMARY KEY (invoice_id, consultation_id),
                FOREIGN KEY (invoice_id) REFERENCES invoices(id),
                FOREIGN KEY (consultation_id) REFERENCES consultations(id)
            );
        ");
    }

    /**
     * Retourne l'objet PDO pour exécuter des requêtes
     */
    public function getPdo(): PDO {
        return $this->pdo;
    }
}