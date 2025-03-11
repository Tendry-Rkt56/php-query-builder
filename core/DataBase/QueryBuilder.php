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

     public function table(string $table): self
     {
         $this->table = $table;
         return $this;
     }

     public function select(array|string $columns = '*'): self
     {
         $this->columns = is_array($columns) ? implode(', ', $columns) : $columns;
         return $this;
     }

     public function where(string $column, string $operator, mixed $value): self
     {
         $this->where[] = "$column $operator ?";
         $this->bindings[] = $value;
         return $this;
     }

     public function orderBy(string $column, string $direction = 'ASC'): self
     {
         $this->orderBy = "ORDER BY $column $direction";
         return $this;
     }

     public function limit(int $limit): self
     {
         $this->limit = "LIMIT $limit";
         return $this;
     }

     public function get(): array
     {
         $sql = "SELECT $this->columns FROM $this->table";
 
         if (!empty($this->where)) {
             $sql .= " WHERE " . implode(' AND ', $this->where);
         }
 
         if (!empty($this->orderBy)) {
             $sql .= " $this->orderBy";
         }
 
         if (!empty($this->limit)) {
             $sql .= " $this->limit";
         }
 
         $stmt = $this->database->getConn()->prepare($sql);
         $stmt->execute($this->bindings);
         return $stmt->fetchAll(\PDO::FETCH_ASSOC);
     }

}