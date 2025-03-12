<?php

namespace Core\DataBase;

class QueryBuilder 
{

    // Instance de la classe DataBase pour gérer la connexion PDO
    private DataBase $database;

    // Nom de la table pour effectuer les requêtes
    private string $table;

    // Colonnes à sélectionner (par défaut ' * ' pour toutes les colonnes)
    private string $columns = '*';

    // Conditions WHERE pour filtrer les résultats
    private array $whereClaus = [];

    // Valeurs liées aux paramètres de la requête
    private array $bindings = [];

    // Critères d'ordonnancement des résultats
    private string $orderBy = '';

    // Limite du nombre de résultats
    private string $limit = '';

    /**
     * Constructeur de la classe QueryBuilder
     * 
     * @param DataBase $pdo Instance de la classe DataBase pour la connexion à la base de données.
     */
    public function __construct(DataBase $pdo)
    {
        $this->database = $pdo;
    }

    /**
     * Définit la table pour la requête.
     * 
     * @param string $table Nom de la table.
     * @return self Retourne l'instance actuelle pour permettre la méthode de chaînage.
    */
    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Définit les colonnes à sélectionner dans la requête.
     * 
     * @param array|string $columns Colonnes à sélectionner. Par défaut, sélectionne toutes les colonnes ('*').
     * @return self Retourne l'instance actuelle pour permettre la méthode de chaînage.
    */
    public function select(array|string $columns = '*'): self
    {
        $this->columns = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }

    /**
     * Ajoute une condition WHERE à la requête.
     * 
     * @param string $column Nom de la colonne à conditionner.
     * @param string $operator L'opérateur de comparaison (ex : '=', '>', '<').
     * @param mixed $value La valeur à comparer.
     * @return self Retourne l'instance actuelle pour permettre la méthode de chaînage.
    */
    public function where(string $column, string $operator, mixed $value): self
    {
        $this->whereClaus[] = ["AND", "$column $operator ?"];
        $this->bindings[] = $value;
        return $this;
    }

    /**
     * Ajoute une condition OR WHERE à la requête.
     * 
     * @param string $column Nom de la colonne à conditionner.
     * @param string $operator L'opérateur de comparaison.
     * @param mixed $value La valeur à comparer.
     * @return self Retourne l'instance actuelle pour permettre la méthode de chaînage.
    */
    public function orWhere(string $column, string $operator, mixed $value):self
    {
        $this->whereClaus[] = ["OR", "$column $operator ?"];
        $this->bindings[] = $value;
        return $this;
    }

    /**
     * Définit les critères de tri (ORDER BY) pour la requête.
     * 
     * @param string $column Colonne par laquelle trier les résultats.
     * @param string $direction Direction du tri (ASC ou DESC).
     * @return self Retourne l'instance actuelle pour permettre la méthode de chaînage.
    */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy = "ORDER BY $column $direction";
        return $this;
    }

    /**
     * Définit la limite des résultats à récupérer.
     * 
     * @param int $limit Nombre de résultats maximum à récupérer.
     * @return self Retourne l'instance actuelle pour permettre la méthode de chaînage.
    */
    public function limit(int $limit): self
    {
        $this->limit = "LIMIT $limit";
        return $this;
    }

    /**
     * Exécute la requête SELECT et retourne les résultats sous forme de tableau.
     * 
     * @return array Résultats de la requête sous forme de tableau associatif.
    */
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

    /**
     * Récupère une seule ligne de résultat de la base de données en fonction des critères spécifiés.
     *
     * Cette méthode exécute une requête `SELECT` sur la table spécifiée à l'aide des colonnes choisies, 
     * avec des conditions `WHERE`, `ORDER BY` et `LIMIT` si elles ont été définies via la méthode de construction 
     * de la requête. Elle retourne le premier résultat sous forme de tableau associatif.
     *
     * Si aucune ligne ne correspond aux critères, la méthode retournera `false`.
     *
     * @return mixed Le premier résultat de la requête sous forme de tableau associatif ou `false` si aucun résultat trouvé.
     *
     * @throws \PDOException Si la requête échoue lors de l'exécution.
    */
    public function getOne(): mixed
    {
        $sql = "SELECT $this->columns FROM $this->table";

        if (!empty($this->whereClaus)) {
            $sql .= " WHERE " . implode(' ', array_map(function($whereClause) {
                return $whereClause[1];
            }, $this->whereClaus));
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



    /**
     * Réinitialise les conditions WHERE et les valeurs liées pour les prochaines requêtes.
     * Cette méthode permet de ne pas réutiliser des conditions incorrectes après une requête.
     * 
     * @return void
    */
    private function clearWhere(): void
    {
        $this->whereClaus = [];
        $this->bindings = [];
    }

    /**
     * Exécute une requête INSERT pour insérer des données dans la table.
     * 
     * @param array $data Données à insérer sous forme de tableau associatif.
     * @return bool Retourne true si l'insertion est réussie, false sinon.
    */
    public function insert(array $data): bool
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO $this->table ($columns) VALUES ($placeholders)";
        $stmt = $this->database->getConn()->prepare($sql);
        return $stmt->execute(array_values($data));
    }

    /**
     * Exécute une requête UPDATE pour mettre à jour les données dans la table.
     * 
     * @param array $data Données à mettre à jour sous forme de tableau associatif.
     * @return bool Retourne true si la mise à jour est réussie, false sinon.
    */
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


    /**
     * Exécute une requête DELETE pour supprimer des données dans la table.
     * 
     * @return bool Retourne true si la suppression est réussie, false sinon.
    */
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