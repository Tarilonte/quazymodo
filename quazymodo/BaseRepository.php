<?php

namespace Quazymodo;

use Quazymodo\Database as DB;

class BaseRepository
{
    private string $hostAlias;
    private string $database;
    private string $table;

    public function __construct($hostAlias, $database, $table)
    {
        $this->hostAlias = $hostAlias;
        $this->database = $database;
        $this->table = $table;
    }

    private function connect()
    {
        return DB::with($this->hostAlias, $this->database);
    }

    /**
     * Set a different table to use for queries
     * @param string $table
     * @return BaseRepository
     */
    public function useTable(string $table): BaseRepository
    {
        $clone = clone $this;
        $clone->table = $table;
        return $clone;
    }

    /**
     * Find a record by id
     * @param int $id
     * @return array
     */
    public function findById(int $id)
    {
        return $this->connect()->get($this->table, '*', ['id' => $id]);
    }

    /**
     * Find all records by criteria, may include limit and offset
     * @param array $criteria
     * @param int $offset
     * @param int|null $limit
     * @return array
     */
    public function findAll(array $criteria = [], int $offset = 0, ?int $limit = null)
    {
        $options = [
            'LIMIT' => [$offset, $limit]
        ];
        $criteria = array_merge($criteria, $options);
        return $this->connect()->select($this->table, '*', $criteria);
    }

    /**
     * Create a new record, will return the id of the new record if $returnId is true
     * @param array $data
     * @param bool $returnId
     * @return int|bool
     */
    public function create(array $data, bool $returnId = false)
    {
        $statement =  $this->connect()->insert($this->table, $data);
        if ($returnId) {
            return $statement->id();
        }
        return $statement;
    }

    /**
     * Update a record by id
     * @param array $data
     * @param int $id
     * @return int
     */
    public function updateById(array $data, int $id)
    {
        return $this->connect()->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete a record by criteria
     * @param array $criteria
     * @return int
     */
    public function delete(array $criteria)
    {
        return $this->connect()->delete($this->table, $criteria);
    }

    /**
     * Count records by criteria
     * @param array $criteria
     * @return int
     */
    public function count(array $criteria = [])
    {
        return $this->connect()->count($this->table, $criteria);
    }

    /**
     * Check if a record exists by id
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        return $this->connect()->has($this->table, ['id' => $id]);
    }

    /**
     * Find one record by criteria
     * @param array $criteria
     * @return array
     */
    public function findOneBy(array $criteria)
    {
        return $this->connect()->get($this->table, '*', $criteria);
    }

    /**
     * Search for records by query
     * Example: search('john', ['first_name', 'last_name']) = SELECT * FROM users WHERE first_name LIKE '%john%' OR last_name LIKE '%john%'
     * @param string $query
     * @param array $fields
     * @return array
     */
    public function search(string $query, array $fields)
    {
        $searchCriteria = [];
        foreach ($fields as $field) {
            $searchCriteria["{$field}[~]"] = $query;
        }
        return $this->connect()->select($this->table, '*', ['OR' => $searchCriteria]);
    }

    /**
     * Find records by criteria
     * Example: findBy(['first_name' => 'John', 'last_name' => 'Doe']) = SELECT * FROM users WHERE first_name = 'John' AND last_name = 'Doe'
     * @param array $criteria
     * @return array
     */
    public function findBy(array $criteria)
    {
        return $this->connect()->select($this->table, '*', $criteria);
    }

    public function getSchema(): array
    {
        return DB::getSchema($this->hostAlias, $this->database);
    }
}
