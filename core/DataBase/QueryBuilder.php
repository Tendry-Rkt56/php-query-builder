<?php

namespace Core\DataBase;

class QueryBuilder 
{

    private DataBase $database;
    private string $table;
    private string $columns = '*';
    private array $whereClaus = [];
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
        $this->whereClaus[] = ["AND", "$column $operator ?"];
        $this->bindings[] = $value;
        return $this;
    }

    public function orWhere(string $column, string $operator, mixed $value):self
    {
        $this->whereClaus[] = ["OR", "$column $operator ?"];
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
 
        if (!empty($this->whereClaus)) {
            $whereParts = [];
            foreach($this->whereClaus as [$conditionType, $claus]) {
                if (empty($whereParts)) {
                    $whereParts[] = $claus;
                }
                else {
                    $whereParts[] = "$conditionType $claus";
                }
            }
            $sql .= " WHERE " . implode(' ', $whereParts);
        }
 
        if (!empty($this->orderBy)) {
            $sql .= " $this->orderBy";
        }
 
        if (!empty($this->limit)) {
            $sql .= " $this->limit";
        }
 
        $stmt = $this->database->getConn()->prepare($sql);
        $stmt->execute($this->bindings);
        $this->clearWhere();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getOne(): array
    {
        $sql = "SELECT $this->columns FROM $this->table";
 
        if (!empty($this->whereClaus)) {
            $whereParts = [];
            foreach($this->whereClaus as [$conditionType, $claus]) {
                if (empty($whereParts)) {
                    $whereParts[] = $claus;
                }
                else {
                    $whereParts[] = "$conditionType $claus";
                }
            }
            $sql .= " WHERE " . implode(' ', $whereParts);
        }
 
        if (!empty($this->orderBy)) {
            $sql .= " $this->orderBy";
        }
 
        if (!empty($this->limit)) {
            $sql .= " $this->limit";
        }
 
        $stmt = $this->database->getConn()->prepare($sql);
        $stmt->execute($this->bindings);
        $this->clearWhere();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    private function clearWhere(): void
    {
        $this->whereClaus = [];
        $this->bindings = [];
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
        if (empty($this->whereClaus)) {
            throw new \Exception("Une condition WHERE est requise pour UPDATE");
        }

        $setParts = [];
        foreach ($data as $column => $value) {
            $setParts[] = "$column = ?";
            $this->bindings[] = $value; // Ajouter les valeurs de mise à jour
        }

        $query = "UPDATE {$this->table} SET " . implode(", ", $setParts);

        $whereParts = [];
        foreach ($this->whereClaus as [$conditionType, $clause]) {
            if (empty($whereParts)) {
                $whereParts[] = $clause; // Première condition sans AND/OR
            } else {
                $whereParts[] = "$conditionType $clause";
            }
        }

        $query .= " WHERE " . implode(" ", $whereParts);

        $stmt = $this->database->getConn()->prepare($query);
        $this->clearWhere();
        return $stmt->execute($this->bindings);
    }


    public function delete(): bool
    {
        if (empty($this->whereClaus)) {
            throw new \Exception("Une condition WHERE est requise pour DELETE");
        }

        $query = "DELETE FROM {$this->table}";

        $whereParts = [];
        foreach ($this->whereClaus as [$conditionType, $clause]) {
            if (empty($whereParts)) {
                $whereParts[] = $clause;
            } else {
                $whereParts[] = "$conditionType $clause";
            }
        }

        $query .= " WHERE " . implode(" ", $whereParts);

        $stmt = $this->database->getConn()->prepare($query);
        $result =  $stmt->execute($this->bindings);
        $this->clearWhere();
        return $result;
    }

    


}