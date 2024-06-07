<?php

namespace Funcoes\Lib;

class DAO
{
    protected DBManager $dbManager;

    protected \PDO $default;

    public function __construct()
    {
        global $dbManager;

        $this->dbManager = $dbManager;
    }

    public static function preparedInsert(string $table, array $record): array
    {
        $columns = implode(", ", array_keys($record));
        $placeholders = self::insertPlaceholders($record);
        return [
            "INSERT INTO $table($columns) VALUES($placeholders)",
            array_values($record),
        ];
    }

    public function preparedBatchInsert(string $table, array $records): array
    {
        $columns = implode(", ", array_keys($records[0]));

        $placeholders = [];
        $args = [];
        foreach ($records as $record) {
            $placeholders[] = "(" . self::insertPlaceholders($record) . ")";
            $args = array_merge($args, array_values($record));
        }
        $placeholders = implode(",", $placeholders);

        return [
            "INSERT INTO $table($columns) VALUES $placeholders",
            $args,
        ];
    }

    private static function insertPlaceholders(array $record): string
    {
        return implode(", ", array_fill(0, count($record), '?'));
    }

    public function preparedUpdate(string $table, array $record): array
    {
        $sets = array_map(function ($field) {
            return "$field = ?";
        }, array_keys($record));
        return [
            "UPDATE $table SET " . implode(', ', $sets),
            array_values($record),
        ];
    }

    public function table($dbName, $tableName, $connection = 'default'): string
    {
        global $config;
        $suffix = $config->get("db.$connection.db_suffix");
        return  "$dbName$suffix.$tableName";
    }

    public function externalTable($system, $tableName): string
    {
        global $config;
        $db = $config->get("$system.db");
        return "$db.$tableName";
    }

    public function paginate($query, $limit = 10, $offset = 0, $orderBy = 1)
    {
        /*
        return "
            SELECT *
            FROM ($query) t 
            ORDER BY $orderBy 
            LIMIT $offset, $limit";
        //OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY";
        */

        return "
            SELECT *, COUNT(*) OVER() as total 
            FROM ($query) t 
            ORDER BY $orderBy 
            OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY";
    }
}
