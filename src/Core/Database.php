<?php
/**
 * Database Connection Manager
 */

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private static ?PDO $connection = null;
    private static array $queryLog = [];
    private static bool $logging = false;

    /**
     * Get PDO connection instance
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            self::connect();
        }
        return self::$connection;
    }

    /**
     * Connect to database
     */
    private static function connect(): void
    {
        $config = config('database');

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['name'],
            $config['charset']
        );

        try {
            self::$connection = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config['charset']} COLLATE {$config['collation']}",
            ]);

            self::$logging = config('app.debug', false);
        } catch (PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Execute a query with parameters
     */
    public static function query(string $sql, array $params = []): PDOStatement
    {
        $startTime = microtime(true);

        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);

        if (self::$logging) {
            self::$queryLog[] = [
                'sql' => $sql,
                'params' => $params,
                'time' => round((microtime(true) - $startTime) * 1000, 2),
            ];
        }

        return $stmt;
    }

    /**
     * Execute SELECT and fetch all results
     */
    public static function select(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    /**
     * Execute SELECT and fetch single row
     */
    public static function selectOne(string $sql, array $params = []): ?array
    {
        $result = self::query($sql, $params)->fetch();
        return $result ?: null;
    }

    /**
     * Execute SELECT and fetch single value
     */
    public static function selectValue(string $sql, array $params = []): mixed
    {
        $result = self::query($sql, $params)->fetchColumn();
        return $result !== false ? $result : null;
    }

    /**
     * Execute INSERT and return last insert ID
     */
    public static function insert(string $table, array $data): int
    {
        $columns = array_keys($data);
        $quotedColumns = array_map(fn($col) => "`$col`", $columns);
        $placeholders = array_map(fn($col) => ":$col", $columns);

        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $table,
            implode(', ', $quotedColumns),
            implode(', ', $placeholders)
        );

        self::query($sql, $data);
        return (int) self::getConnection()->lastInsertId();
    }

    /**
     * Execute batch INSERT
     */
    public static function insertBatch(string $table, array $rows): int
    {
        if (empty($rows)) {
            return 0;
        }

        $columns = array_keys($rows[0]);
        $quotedColumns = array_map(fn($col) => "`$col`", $columns);
        $placeholders = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
        $allPlaceholders = implode(', ', array_fill(0, count($rows), $placeholders));

        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES %s',
            $table,
            implode(', ', $quotedColumns),
            $allPlaceholders
        );

        $values = [];
        foreach ($rows as $row) {
            foreach ($columns as $col) {
                $values[] = $row[$col] ?? null;
            }
        }

        self::query($sql, $values);
        return count($rows);
    }

    /**
     * Execute UPDATE
     * Supports both positional (?) and named (:param) placeholders in WHERE clause
     */
    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $sets = array_map(fn($col) => "`$col` = :set_$col", array_keys($data));
        $setParams = [];
        foreach ($data as $key => $value) {
            $setParams["set_$key"] = $value;
        }

        $params = $setParams;

        // Check if WHERE clause uses positional (?) or named (:param) placeholders
        if (str_contains($where, '?')) {
            // Convert positional parameters in WHERE clause to named parameters
            $whereParamCount = substr_count($where, '?');
            $namedWhereParams = [];
            for ($i = 0; $i < $whereParamCount; $i++) {
                $paramName = ":where_$i";
                $where = preg_replace('/\?/', $paramName, $where, 1);
                $namedWhereParams[$paramName] = $whereParams[$i] ?? null;
            }
            $params = array_merge($params, $namedWhereParams);
        } else {
            // Named placeholders - merge whereParams directly
            $params = array_merge($params, $whereParams);
        }

        $sql = sprintf('UPDATE `%s` SET %s WHERE %s', $table, implode(', ', $sets), $where);

        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Execute DELETE
     * Supports both positional (?) and named (:param) placeholders in WHERE clause
     */
    public static function delete(string $table, string $where, array $params = []): int
    {
        $finalParams = [];

        // Check if WHERE clause uses positional (?) or named (:param) placeholders
        if (str_contains($where, '?')) {
            // Convert positional parameters in WHERE clause to named parameters
            $whereParamCount = substr_count($where, '?');
            for ($i = 0; $i < $whereParamCount; $i++) {
                $paramName = ":where_$i";
                $where = preg_replace('/\?/', $paramName, $where, 1);
                $finalParams[$paramName] = $params[$i] ?? null;
            }
        } else {
            // Named placeholders - use params directly
            $finalParams = $params;
        }

        $sql = sprintf('DELETE FROM `%s` WHERE %s', $table, $where);
        $stmt = self::query($sql, $finalParams);
        return $stmt->rowCount();
    }

    /**
     * Execute INSERT OR UPDATE (UPSERT) using MySQL's INSERT ... ON DUPLICATE KEY UPDATE
     * Used for notification preferences and similar upsert scenarios
     * Compatible with MySQL 5.7+ and 8.0+
     * 
     * @param string $table Table name
     * @param array $data Data to insert/update
     * @param array $uniqueKeys Unique key columns for identifying duplicates
     * @return bool True if inserted/updated successfully
     */
    public static function insertOrUpdate(string $table, array $data, array $uniqueKeys = []): bool
    {
        $columns = array_keys($data);
        $quotedColumns = array_map(fn($col) => "`$col`", $columns);

        // Use positional parameters (?) instead of named parameters (:col)
        // This avoids PDO parameter binding issues with ON DUPLICATE KEY UPDATE
        // when the same placeholder would appear in both VALUES and UPDATE clauses
        $placeholders = array_fill(0, count($columns), '?');

        // Build UPDATE clause for duplicate key update
        // Use column names with VALUES() function for clarity, or direct column references
        // Note: MySQL ON DUPLICATE KEY UPDATE uses direct parameter binding
        $updateClauses = [];
        foreach ($columns as $col) {
            // Don't update the unique key columns
            if (!in_array($col, $uniqueKeys)) {
                // Use VALUES() function for compatibility with all MySQL versions
                // VALUES(col) refers to the value that would be inserted
                $updateClauses[] = "`$col` = VALUES(`$col`)";
            }
        }

        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s',
            $table,
            implode(', ', $quotedColumns),
            implode(', ', $placeholders),
            implode(', ', $updateClauses)
        );

        // Convert $data to ordered array for positional parameters
        $params = array_values($data);

        if (count($params) !== count($placeholders)) {
            throw new \RuntimeException(sprintf(
                'Invalid parameter number in insertOrUpdate: Expected %d, got %d. Columns: %s',
                count($placeholders),
                count($params),
                implode(', ', $columns)
            ));
        }

        $stmt = self::query($sql, $params);
        return $stmt->rowCount() > 0;
    }

    /**
     * Begin transaction
     */
    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }

    /**
     * Rollback transaction
     */
    public static function rollback(): bool
    {
        return self::getConnection()->rollBack();
    }

    /**
     * Execute callback within transaction
     */
    public static function transaction(callable $callback): mixed
    {
        self::beginTransaction();

        try {
            $result = $callback();
            self::commit();
            return $result;
        } catch (\Throwable $e) {
            self::rollback();
            throw $e;
        }
    }

    /**
     * Check if table exists
     */
    public static function tableExists(string $table): bool
    {
        $sql = "SHOW TABLES LIKE :table";
        return self::selectOne($sql, ['table' => $table]) !== null;
    }

    /**
     * Get query log
     */
    public static function getQueryLog(): array
    {
        return self::$queryLog;
    }

    /**
     * Clear query log
     */
    public static function clearQueryLog(): void
    {
        self::$queryLog = [];
    }

    /**
     * Get total query time
     */
    public static function getTotalQueryTime(): float
    {
        return array_sum(array_column(self::$queryLog, 'time'));
    }
}
