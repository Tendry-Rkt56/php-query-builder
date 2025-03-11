<?php

namespace App;

use Core\DataBase\DataBase;

class App 
{

     private static $_instance;
     private static $_db;

     public function getInstance(): self
     {
          if (self::$_instance == null) self::$_instance = new self();
          return self::$_instance;
     }

     public function getDb(): DataBase
     {
          if (self::$_db == null) self::$_db = new DataBase(DB_NAME);
          return self::$_db;
     }

     public function getEntity(string $class)
     {
          return new $class($this->getDb());
     }

}