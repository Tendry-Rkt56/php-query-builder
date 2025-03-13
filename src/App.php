<?php

namespace App;

use Core\DataBase\DataBase;
use Core\DataBase\QueryBuilder;

/**
 * Classe principale de l'application, implémentant un singleton.
 *
 * @template T of object
 */
class App 
{

     /**
     * Instance unique de l'application.
     *
     * @var self|null
     */
     private static $_instance;
     
     /**
     * Instance de la base de données.
     *
     * @var DataBase|null
     */
     private static $_db;
     
     /**
     * Instance du QueryBuilder.
     *
     * @var QueryBuilder|null
     */
     private static $_builder;

     public static function getInstance(): self
     {
          if (self::$_instance == null) self::$_instance = new self();
          return self::$_instance;
     }

     public function getDb(): DataBase
     {
          if (self::$_db == null) self::$_db = new DataBase(DB_NAME);
          return self::$_db;
     }

     public function getQueryBuilder(): QueryBuilder
     {
          if (self::$_builder == null) self::$_builder = new QueryBuilder($this->getDb());
          return self::$_builder;
     }

     /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
     public function getEntity(string $class)
     {
          return new $class($this->getDb());
     }

}