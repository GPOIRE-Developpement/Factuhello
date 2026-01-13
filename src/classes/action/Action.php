<?php

namespace guillaumepaquin\factuhello\action;

/**
 * Classe abstraite de base pour toutes les actions
 * Stocke les informations de la requête HTTP
 */
abstract class Action {
    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;

    public function __construct(){
        $this->http_method = $_SERVER['REQUEST_METHOD'] ?? null;
        $this->hostname = $_SERVER['HTTP_HOST'] ?? null;
        $this->script_name = $_SERVER['SCRIPT_NAME'] ?? null;
    }

    /**
     * Exécute l'action et retourne le HTML à afficher
     * @return string HTML généré
     */
    abstract public static function execute() : string;
}