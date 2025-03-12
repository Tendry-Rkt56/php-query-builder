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

     /**
      * Récupère un enregistrement en particulier grâce à son id
      * @param int $id
      * @return mixed
      */
     public function find(int $id)
     {
          return $this->table($this->table)->where('id', '=', $id)->getOne();
     }

     /**
      * Supprime un enregistrement
      * @param int $id
      * @return bool
      */
     public function remove(int $id): bool
     {
          return $this->table($this->table)->where('id', '=', $id)->delete();
     }


}