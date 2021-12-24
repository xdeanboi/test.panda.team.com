<?php

namespace PandaTeam\Models;

use PandaTeam\Services\Db;

abstract class ActiveRecordEntity
{
    protected $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    abstract protected static function getTableName(): string;

    public function __set($name, $value)
    {
        $nameToCamelCase = $this->underscoreToCamelCase($name);
        $this->$nameToCamelCase = $value;
    }

    private function camelCaseToUnderscore(string $source): string
    {
        //camelCase => camel_case
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $source));
    }

    private function underscoreToCamelCase(string $source): string
    {
        //camel_case => camelCase
        return lcfirst(str_replace('_', '', ucwords($source, '_')));
    }

    private function mapPropertiesToDb(): array
    {
        $reflector = new \ReflectionObject($this);
        $properties = $reflector->getProperties();

        $mappedProperties = [];
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyToUnderscore = $this->camelCaseToUnderscore($propertyName);
            $mappedProperties[$propertyToUnderscore] = $this->$propertyName;
        }

        return $mappedProperties;
    }

    public function save(): void
    {
        $mappedProperties = $this->mapPropertiesToDb();

        if ($this->id !== null) {
            $this->update($mappedProperties);
        } else {
            $this->insert($mappedProperties);
        }
    }

    private function update(array $mappedProperties): void
    {
        /*
         *UPDATE tableName SET column1 = value1, column2 = value2 WHERE id=:id;
         *column1 = :param1
         *[:param1 = :value1]
         */

        $columns2params = [];
        $params2value = [];
        $index = 1;

        foreach ($mappedProperties as $propertyName => $value) {
            $params = ':param' . $index++;
            $params2value[$params] = $value;
            $columns2params[] = $propertyName . ' = ' . $params;
        }

        $valueForSet = implode(', ', $columns2params);
        $sql = 'UPDATE ' . static::getTableName() . ' SET ' . $valueForSet . ' WHERE id=' . $this->id;

        $db = Db::getInstance();
        $db->query($sql, $params2value, static::class);

    }

    private function insert(array $mappedProperties): void
    {
        /*add
         *INSERT INTO tableName (column1, column2) VALUES (value1, value2);
         *[:param1 => value1]
         */

        $filerProperties = array_filter($mappedProperties);
        $columns = [];
        $params = [];
        $params2values = [];
        $index = 1;


        foreach ($filerProperties as $columnName => $value) {
            $param = ':param' . $index++;
            $columns[] = $columnName;
            $params2values[$param] = $value;
            $params[] = $param;
        }

        $columnsViaSemicolon = implode(', ', $columns);
        $paramsViaSemicolon = implode(', ', $params);

        $db = Db::getInstance();
        $sql = 'INSERT INTO ' . static::getTableName() . ' ( ' . $columnsViaSemicolon . ' ) VALUES ( ' . $paramsViaSemicolon . ' );';

        $db->query($sql, $params2values, static::class);
        $this->id = $db->getLastId();
    }

    public static function findAll(): ?array
    {
        $db = Db::getInstance();

        $result = $db->query('SELECT * FROM `' . static::getTableName() . '`;', [], static::class);

        if (empty($result)) {
            return null;
        }

        return $result;
    }

    public static function getById(int $id): ?self
    {
        $db = Db::getInstance();

        $result = $db->query('SELECT * FROM `' . static::getTableName() . '` WHERE id=:id ',
            [':id' => $id],
            static::class);

        return $result ? $result[0] : null;
    }

    public static function findByOneColumn(string $columnName, $value): ?self
    {
        $db = Db::getInstance();

        $sql = 'SELECT * FROM`' . static::getTableName() . '` WHERE ' . $columnName . ' = :value LIMIT 1;';
        $result = $db->query($sql, [':value' => $value], static::class);

        if ($result === []) {
            return null;
        }

        return $result[0];
    }

    public function delete(): void
    {
        $db = Db::getInstance();

        $db->query('DELETE FROM ' . static::getTableName() . ' WHERE id=:id',
            [':id' => $this->id],
            static::class);
    }
}