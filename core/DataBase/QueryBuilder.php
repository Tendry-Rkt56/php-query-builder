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

     public function insert(array $data): bool
     {
          $columns = implode(', ', array_keys($data));
          $placeholders = implode(', ', array_fill(0, count($data), '?'));

          $sql = "INSERT INTO $this->table ($columns) VALUES ($placeholders)";
          $stmt = $this->database->getConn()->prepare($sql);
          return $stmt->execute(array_values($data));
     }

     public function update(array $data): bool
     {
          $set = implode(', ', array_map(fn($key) => "$key = ?", array_keys($data)));
          $sql = "UPDATE $this->table SET $set";

          if (!empty($this->where)) {
               $sql .= " WHERE " . implode(' AND ', $this->where);
          }

          $stmt = $this->database->getConn()->prepare($sql);
          return $stmt->execute(array_merge(array_values($data), $this->bindings));
     }

     public function delete(): bool
     {
          $sql = "DELETE FROM $this->table";

          if (!empty($this->where)) {
               $sql .= " WHERE " . implode(' AND ', $this->where);
          }

          $stmt = $this->database->getConn()->prepare($sql);
          return $stmt->execute($this->bindings);
     }

}