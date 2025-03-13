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

     public function __construct(string $dbName)
     {
          $this->dbName = $dbName;
          $this->conn = new \PDO("mysql:host=".DB_HOST.";dbname=".$this->dbName.";charset=".DB_CHARSET, DB_USER, DB_PASS);
     }

     public function getConn(): \PDO
     {
          return $this->conn;
     }

}