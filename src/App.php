<?php

namespace App;

use Core\DataBase\DataBase;
use Core\DataBase\QueryBuilder;

class App 
{

     private static $_instance;
     private static $_db;
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

     public function getQueryBuilder()
     {
          if (self::$_builder == null) self::$_builder = new QueryBuilder($this->getDb());
          return self::$_builder;
     }

     public function getEntity(string $class)
     {
          return new $class($this->getDb());
     }

}