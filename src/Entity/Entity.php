<?php

namespace App\Entity;

use Core\DataBase\DataBase;
use Core\DataBase\QueryBuilder;

class Entity extends QueryBuilder
{

     public function __construct(private DataBase $db)
     {
          parent::__construct($db);
     }


}