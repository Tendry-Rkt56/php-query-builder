<?php

namespace Core\DataBase;


/**
 * Classe de gestion de la connexion à la base de données.
 */
class DataBase 
{

     /**
     * Instance de connexion PDO.
     *
     * @var \PDO
     */
     private $conn;
     
     /**
     * Nom de la base de données.
     *
     * @var string
     */
     private string $dbName;

     /**
     * Constructeur de la classe DataBase.
     *
     * Initialise la connexion à la base de données en utilisant les constantes de configuration.
     *
     * @param string $dbName Le nom de la base de données à utiliser.
     * @throws \PDOException En cas d'échec de connexion à la base de données.
     */
     public function __construct(string $dbName)
     {
          $this->dbName = $dbName;
          $this->conn = new \PDO("mysql:host=".DB_HOST.";dbname=".$this->dbName.";charset=".DB_CHARSET, DB_USER, DB_PASS);
     }

     /**
     * Retourne l'instance de connexion PDO.
     *
     * @return \PDO L'objet PDO représentant la connexion à la base de données.
     */
     public function getConn(): \PDO
     {
          return $this->conn;
     }

}