<?php
/**
 * Fluent Query Builder
 */

declare(strict_types=1);

namespace App\Core;

class QueryBuilder
{
    private string $table;
    private array $columns = ['*'];
    private array $joins = [];
    private array $wheres = [];
    private array $orWheres = [];
    private array $params = [];
    private array $orderBy = [];
    private array $groupBy = [];
    private array $having = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private bool $distinct = false;
    private int $paramCounter = 0;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * Static constructor
     */
    public static function table(string $table): self
    {
        return new self($table);
    }

    /**
     * Select columns
     */
    public function select(string|array $columns = ['*']): self
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    /**
     * Add select columns
     */
    public function addSelect(string|array $columns): self
    {
        $columns = is_array($columns) ? $columns : func_get_args();
        $this->columns = array_merge($this->columns, $columns);
        return $this;
    }

    /**
     * Select distinct
     */
    public function distinct(): self
    {
        $this->distinct = true;
        return $this;
    }

    /**
     * Add where clause
     */
    public function where(string $column, mixed $operator = null, mixed $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $param = $this->nextParam();
        $this->wheres[] = "$column $operator :$param";
        $this->params[$param] = $value;

        return $this;
    }

    /**
     * Add or where clause
     */
    public function orWhere(string $column, mixed $operator = null, mixed $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $param = $this->nextParam();
        $this->orWheres[] = "$column $operator :$param";
        $this->params[$param] = $value;

        return $this;
    }

    /**
     * Where in
     */
    public function whereIn(string $column, array $values): self
    {
        if (empty($values)) {
            $this->wheres[] = '1 = 0'; // Always false
            return $this;
        }

        $placeholders = [];
        foreach ($values as $value) {
            $param = $this->nextParam();
            $placeholders[] = ":$param";
            $this->params[$param] = $value;
        }

        $this->wheres[] = "$column IN (" . implode(', ', $placeholders) . ')';

        return $this;
    }

    /**
     * Where not in
     */
    public function whereNotIn(string $column, array $values): self
    {
        if (empty($values)) {
            return $this;
        }

        $placeholders = [];
        foreach ($values as $value) {
            $param = $this->nextParam();
            $placeholders[] = ":$param";
            $this->params[$param] = $value;
        }

        $this->wheres[] = "$column NOT IN (" . implode(', ', $placeholders) . ')';

        return $this;
    }

    /**
     * Where null
     */
    public function whereNull(string $column): self
    {
        $this->wheres[] = "$column IS NULL";
        return $this;
    }

    /**
     * Where not null
     */
    public function whereNotNull(string $column): self
    {
        $this->wheres[] = "$column IS NOT NULL";
        return $this;
    }

    /**
     * Where between
     */
    public function whereBetween(string $column, mixed $min, mixed $max): self
    {
        $param1 = $this->nextParam();
        $param2 = $this->nextParam();
        $this->wheres[] = "$column BETWEEN :$param1 AND :$param2";
        $this->params[$param1] = $min;
        $this->params[$param2] = $max;

        return $this;
    }

    /**
     * Where like
     */
    public function whereLike(string $column, string $value): self
    {
        $param = $this->nextParam();
        $this->wheres[] = "$column LIKE :$param";
        $this->params[$param] = $value;

        return $this;
    }

    /**
     * Where raw SQL
     */
    public function whereRaw(string $sql, array $params = []): self
    {
        $this->wheres[] = $sql;
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * Add join
     */
    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): self
    {
        $this->joins[] = "$type JOIN $table ON $first $operator $second";
        return $this;
    }

    /**
     * Left join
     */
    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    /**
     * Right join
     */
    public function rightJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }

    /**
     * Order by
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = "$column $direction";
        return $this;
    }

    /**
     * Order by descending
     */
    public function orderByDesc(string $column): self
    {
        return $this->orderBy($column, 'DESC');
    }

    /**
     * Latest (order by created_at desc)
     */
    public function latest(string $column = 'created_at'): self
    {
        return $this->orderByDesc($column);
    }

    /**
     * Oldest (order by created_at asc)
     */
    public function oldest(string $column = 'created_at'): self
    {
        return $this->orderBy($column, 'ASC');
    }

    /**
     * Group by
     */
    public function groupBy(string|array $columns): self
    {
        $this->groupBy = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    /**
     * Having
     */
    public function having(string $column, string $operator, mixed $value): self
    {
        $param = $this->nextParam();
        $this->having[] = "$column $operator :$param";
        $this->params[$param] = $value;
        return $this;
    }

    /**
     * Limit
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Offset
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Take (alias for limit)
     */
    public function take(int $count): self
    {
        return $this->limit($count);
    }

    /**
     * Skip (alias for offset)
     */
    public function skip(int $count): self
    {
        return $this->offset($count);
    }

    /**
     * For page
     */
    public function forPage(int $page, int $perPage = 25): self
    {
        return $this->limit($perPage)->offset(($page - 1) * $perPage);
    }

    /**
     * Build select SQL
     */
    private function buildSelectSql(): string
    {
        $sql = 'SELECT ';
        
        if ($this->distinct) {
            $sql .= 'DISTINCT ';
        }
        
        $sql .= implode(', ', $this->columns);
        $sql .= ' FROM ' . $this->table;

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        $sql .= $this->buildWhere();

        if (!empty($this->groupBy)) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groupBy);
        }

        if (!empty($this->having)) {
            $sql .= ' HAVING ' . implode(' AND ', $this->having);
        }

        if (!empty($this->orderBy)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset !== null) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $sql;
    }

    /**
     * Build where clause
     */
    private function buildWhere(): string
    {
        $conditions = $this->wheres;

        if (!empty($this->orWheres)) {
            $conditions[] = '(' . implode(' OR ', $this->orWheres) . ')';
        }

        if (empty($conditions)) {
            return '';
        }

        return ' WHERE ' . implode(' AND ', $conditions);
    }

    /**
     * Get all results
     */
    public function get(): array
    {
        return Database::select($this->buildSelectSql(), $this->params);
    }

    /**
     * Get first result
     */
    public function first(): ?array
    {
        $this->limit(1);
        return Database::selectOne($this->buildSelectSql(), $this->params);
    }

    /**
     * Get first or throw exception
     */
    public function firstOrFail(): array
    {
        $result = $this->first();
        if (!$result) {
            abort(404, 'Record not found');
        }
        return $result;
    }

    /**
     * Get single value
     */
    public function value(string $column): mixed
    {
        $this->columns = [$column];
        return Database::selectValue($this->buildSelectSql(), $this->params);
    }

    /**
     * Pluck column values
     */
    public function pluck(string $column, ?string $key = null): array
    {
        if ($key) {
            $this->columns = [$key, $column];
        } else {
            $this->columns = [$column];
        }

        $results = $this->get();

        if ($key) {
            $plucked = [];
            foreach ($results as $row) {
                $plucked[$row[$key]] = $row[$column];
            }
            return $plucked;
        }

        return array_column($results, $column);
    }

    /**
     * Count results
     */
    public function count(): int
    {
        $this->columns = ['COUNT(*) as count'];
        return (int) ($this->first()['count'] ?? 0);
    }

    /**
     * Sum column
     */
    public function sum(string $column): float
    {
        $this->columns = ["SUM($column) as sum"];
        return (float) ($this->first()['sum'] ?? 0);
    }

    /**
     * Average column
     */
    public function avg(string $column): float
    {
        $this->columns = ["AVG($column) as avg"];
        return (float) ($this->first()['avg'] ?? 0);
    }

    /**
     * Max column
     */
    public function max(string $column): mixed
    {
        $this->columns = ["MAX($column) as max"];
        return $this->first()['max'] ?? null;
    }

    /**
     * Min column
     */
    public function min(string $column): mixed
    {
        $this->columns = ["MIN($column) as min"];
        return $this->first()['min'] ?? null;
    }

    /**
     * Check if exists
     */
    public function exists(): bool
    {
        return $this->count() > 0;
    }

    /**
     * Check if doesn't exist
     */
    public function doesntExist(): bool
    {
        return !$this->exists();
    }

    /**
     * Insert data
     */
    public function insert(array $data): int
    {
        return Database::insert($this->table, $data);
    }

    /**
     * Insert multiple rows
     */
    public function insertBatch(array $rows): int
    {
        return Database::insertBatch($this->table, $rows);
    }

    /**
     * Update data
     */
    public function update(array $data): int
    {
        $where = ltrim($this->buildWhere(), ' WHERE ');
        return Database::update($this->table, $data, $where, $this->params);
    }

    /**
     * Delete records
     */
    public function delete(): int
    {
        $where = ltrim($this->buildWhere(), ' WHERE ');
        return Database::delete($this->table, $where, $this->params);
    }

    /**
     * Increment column
     */
    public function increment(string $column, int $amount = 1): int
    {
        $where = ltrim($this->buildWhere(), ' WHERE ');
        $sql = "UPDATE {$this->table} SET $column = $column + :amount WHERE $where";
        $this->params['amount'] = $amount;
        return Database::query($sql, $this->params)->rowCount();
    }

    /**
     * Decrement column
     */
    public function decrement(string $column, int $amount = 1): int
    {
        return $this->increment($column, -$amount);
    }

    /**
     * Paginate results
     */
    public function paginate(int $perPage = 25, int $page = null): array
    {
        $page = $page ?? max(1, (int) ($_GET['page'] ?? 1));
        $offset = ($page - 1) * $perPage;

        // Get total count
        $countBuilder = clone $this;
        $total = $countBuilder->count();

        // Get results
        $this->limit($perPage)->offset($offset);
        $items = $this->get();

        $lastPage = (int) ceil($total / $perPage);

        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage,
            'has_more' => $page < $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
        ];
    }

    /**
     * Get next parameter name
     */
    private function nextParam(): string
    {
        return 'p' . (++$this->paramCounter);
    }

    /**
     * Get built SQL for debugging
     */
    public function toSql(): string
    {
        return $this->buildSelectSql();
    }

    /**
     * Get parameters for debugging
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
