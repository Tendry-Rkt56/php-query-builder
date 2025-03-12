<?php

namespace App\Entity;

use Core\DataBase\DataBase;
use Core\DataBase\QueryBuilder;

class Entity extends QueryBuilder
{

     protected string $table = '';

     public function __construct(private DataBase $db)
     {
          parent::__construct($db);
     }

     /**
      * Récupère tous les enregistrements
      * @return array
      */
     public function findAll(): array
     {
          return $this->table($this->table)->get();
     }

     public function find(int $id)
     {
          return $this->table($this->table)->where('id', '=', $id)->get();
     }

     public function remove(int $id)
     {
          return $this->table($this->table)->where('id', '=', $id)->delete();
     }


}