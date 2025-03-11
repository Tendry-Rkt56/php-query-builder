<?php

namespace Core\DataBase;

class QueryBuilder 
{

     private DataBase $database;
     private string $table;
     private string $columns = '*';
     private array $where = [];
     private array $bindings = [];
     private string $orderBy = '';
     private string $limit = '';

     public function __construct(DataBase $pdo)
     {
         $this->database = $pdo;
     }

     

}