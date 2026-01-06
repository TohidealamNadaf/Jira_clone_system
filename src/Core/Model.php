<?php
/**
 * Base Model (Active Record Pattern)
 */

declare(strict_types=1);

namespace App\Core;

abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [];
    protected static array $guarded = ['id'];
    protected static array $hidden = [];
    protected static array $casts = [];
    protected static bool $timestamps = true;
    protected static ?string $createdAt = 'created_at';
    protected static ?string $updatedAt = 'updated_at';
    protected static bool $softDeletes = false;
    protected static ?string $deletedAt = 'deleted_at';

    protected array $attributes = [];
    protected array $original = [];
    protected bool $exists = false;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * Get table name
     */
    public static function getTable(): string
    {
        if (static::$table) {
            return static::$table;
        }

        // Convert class name to table name
        $class = (new \ReflectionClass(static::class))->getShortName();
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $class)) . 's';
    }

    /**
     * Fill attributes
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            }
        }
        return $this;
    }

    /**
     * Check if attribute is fillable
     */
    protected function isFillable(string $key): bool
    {
        if (in_array($key, static::$guarded)) {
            return false;
        }
        
        if (empty(static::$fillable)) {
            return true;
        }

        return in_array($key, static::$fillable);
    }

    /**
     * Set attribute
     */
    public function setAttribute(string $key, mixed $value): void
    {
        // Check for mutator
        $mutator = 'set' . str_replace('_', '', ucwords($key, '_')) . 'Attribute';
        if (method_exists($this, $mutator)) {
            $value = $this->$mutator($value);
        }

        $this->attributes[$key] = $value;
    }

    /**
     * Get attribute
     */
    public function getAttribute(string $key): mixed
    {
        $value = $this->attributes[$key] ?? null;

        // Check for accessor
        $accessor = 'get' . str_replace('_', '', ucwords($key, '_')) . 'Attribute';
        if (method_exists($this, $accessor)) {
            return $this->$accessor($value);
        }

        // Cast attribute
        if (isset(static::$casts[$key])) {
            $value = $this->castAttribute($key, $value);
        }

        return $value;
    }

    /**
     * Cast attribute to type
     */
    protected function castAttribute(string $key, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        $type = static::$casts[$key];

        return match ($type) {
            'int', 'integer' => (int) $value,
            'float', 'double' => (float) $value,
            'string' => (string) $value,
            'bool', 'boolean' => (bool) $value,
            'array', 'json' => is_array($value) ? $value : json_decode($value, true),
            'datetime' => new \DateTime($value),
            'date' => (new \DateTime($value))->format('Y-m-d'),
            default => $value,
        };
    }

    /**
     * Magic getter
     */
    public function __get(string $key): mixed
    {
        return $this->getAttribute($key);
    }

    /**
     * Magic setter
     */
    public function __set(string $key, mixed $value): void
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Magic isset
     */
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Get primary key value
     */
    public function getKey(): mixed
    {
        return $this->getAttribute(static::$primaryKey);
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        $array = [];
        foreach ($this->attributes as $key => $value) {
            if (!in_array($key, static::$hidden)) {
                $array[$key] = $this->getAttribute($key);
            }
        }
        return $array;
    }

    /**
     * Convert to JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Check if model exists in database
     */
    public function exists(): bool
    {
        return $this->exists;
    }

    /**
     * Save model to database
     */
    public function save(): bool
    {
        if ($this->exists) {
            return $this->update();
        }
        return $this->insert();
    }

    /**
     * Insert new record
     */
    protected function insert(): bool
    {
        $attributes = $this->attributes;

        // Add timestamps
        if (static::$timestamps && static::$createdAt) {
            $attributes[static::$createdAt] = date('Y-m-d H:i:s');
        }
        if (static::$timestamps && static::$updatedAt) {
            $attributes[static::$updatedAt] = date('Y-m-d H:i:s');
        }

        // Prepare data
        $data = [];
        foreach ($attributes as $key => $value) {
            if ($key !== static::$primaryKey) {
                $data[$key] = is_array($value) ? json_encode($value) : $value;
            }
        }

        $id = Database::insert(static::getTable(), $data);
        $this->attributes[static::$primaryKey] = $id;
        $this->exists = true;
        $this->original = $this->attributes;

        return true;
    }

    /**
     * Update existing record
     */
    protected function update(): bool
    {
        $attributes = $this->getDirty();

        if (empty($attributes)) {
            return true;
        }

        // Add updated timestamp
        if (static::$timestamps && static::$updatedAt) {
            $attributes[static::$updatedAt] = date('Y-m-d H:i:s');
        }

        // Prepare data
        $data = [];
        foreach ($attributes as $key => $value) {
            $data[$key] = is_array($value) ? json_encode($value) : $value;
        }

        Database::update(
            static::getTable(),
            $data,
            static::$primaryKey . ' = :id',
            ['id' => $this->getKey()]
        );

        $this->original = $this->attributes;

        return true;
    }

    /**
     * Get dirty (changed) attributes
     */
    public function getDirty(): array
    {
        $dirty = [];
        foreach ($this->attributes as $key => $value) {
            if (!isset($this->original[$key]) || $this->original[$key] !== $value) {
                $dirty[$key] = $value;
            }
        }
        return $dirty;
    }

    /**
     * Delete record
     */
    public function delete(): bool
    {
        if (!$this->exists) {
            return false;
        }

        if (static::$softDeletes && static::$deletedAt) {
            $this->attributes[static::$deletedAt] = date('Y-m-d H:i:s');
            return $this->update();
        }

        Database::delete(
            static::getTable(),
            static::$primaryKey . ' = :id',
            ['id' => $this->getKey()]
        );

        $this->exists = false;
        return true;
    }

    /**
     * Force delete (bypass soft deletes)
     */
    public function forceDelete(): bool
    {
        if (!$this->exists) {
            return false;
        }

        Database::delete(
            static::getTable(),
            static::$primaryKey . ' = :id',
            ['id' => $this->getKey()]
        );

        $this->exists = false;
        return true;
    }

    /**
     * Restore soft deleted record
     */
    public function restore(): bool
    {
        if (!static::$softDeletes || !static::$deletedAt) {
            return false;
        }

        $this->attributes[static::$deletedAt] = null;
        return $this->update();
    }

    /**
     * Find by primary key
     */
    public static function find(int|string $id): ?static
    {
        $sql = sprintf(
            'SELECT * FROM %s WHERE %s = :id',
            static::getTable(),
            static::$primaryKey
        );

        if (static::$softDeletes && static::$deletedAt) {
            $sql .= ' AND ' . static::$deletedAt . ' IS NULL';
        }

        $row = Database::selectOne($sql, ['id' => $id]);

        return $row ? static::hydrate($row) : null;
    }

    /**
     * Find by primary key or throw exception
     */
    public static function findOrFail(int|string $id): static
    {
        $model = static::find($id);
        if (!$model) {
            abort(404, 'Record not found');
        }
        return $model;
    }

    /**
     * Get all records
     */
    public static function all(): array
    {
        $sql = 'SELECT * FROM ' . static::getTable();

        if (static::$softDeletes && static::$deletedAt) {
            $sql .= ' WHERE ' . static::$deletedAt . ' IS NULL';
        }

        $rows = Database::select($sql);

        return array_map(fn($row) => static::hydrate($row), $rows);
    }

    /**
     * Get first record matching conditions
     */
    public static function first(array $conditions = []): ?static
    {
        $sql = 'SELECT * FROM ' . static::getTable();
        $params = [];
        $wheres = [];

        if (static::$softDeletes && static::$deletedAt) {
            $wheres[] = static::$deletedAt . ' IS NULL';
        }

        foreach ($conditions as $column => $value) {
            $wheres[] = "$column = :$column";
            $params[$column] = $value;
        }

        if (!empty($wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $wheres);
        }

        $sql .= ' LIMIT 1';

        $row = Database::selectOne($sql, $params);

        return $row ? static::hydrate($row) : null;
    }

    /**
     * Get records matching conditions
     */
    public static function where(array $conditions): array
    {
        $sql = 'SELECT * FROM ' . static::getTable();
        $params = [];
        $wheres = [];

        if (static::$softDeletes && static::$deletedAt) {
            $wheres[] = static::$deletedAt . ' IS NULL';
        }

        foreach ($conditions as $column => $value) {
            if (is_array($value)) {
                $placeholders = [];
                foreach ($value as $i => $v) {
                    $key = "{$column}_{$i}";
                    $placeholders[] = ":$key";
                    $params[$key] = $v;
                }
                $wheres[] = "$column IN (" . implode(', ', $placeholders) . ')';
            } else {
                $wheres[] = "$column = :$column";
                $params[$column] = $value;
            }
        }

        if (!empty($wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $wheres);
        }

        $rows = Database::select($sql, $params);

        return array_map(fn($row) => static::hydrate($row), $rows);
    }

    /**
     * Create new record
     */
    public static function create(array $attributes): static
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    /**
     * Update records matching conditions
     */
    public static function updateWhere(array $conditions, array $data): int
    {
        $wheres = [];
        $params = [];

        foreach ($conditions as $column => $value) {
            $wheres[] = "$column = :where_$column";
            $params["where_$column"] = $value;
        }

        $where = implode(' AND ', $wheres);

        return Database::update(static::getTable(), $data, $where, $params);
    }

    /**
     * Delete records matching conditions
     */
    public static function deleteWhere(array $conditions): int
    {
        $wheres = [];
        $params = [];

        foreach ($conditions as $column => $value) {
            $wheres[] = "$column = :$column";
            $params[$column] = $value;
        }

        $where = implode(' AND ', $wheres);

        if (static::$softDeletes && static::$deletedAt) {
            return Database::update(
                static::getTable(),
                [static::$deletedAt => date('Y-m-d H:i:s')],
                $where,
                $params
            );
        }

        return Database::delete(static::getTable(), $where, $params);
    }

    /**
     * Count records
     */
    public static function count(array $conditions = []): int
    {
        $sql = 'SELECT COUNT(*) FROM ' . static::getTable();
        $params = [];
        $wheres = [];

        if (static::$softDeletes && static::$deletedAt) {
            $wheres[] = static::$deletedAt . ' IS NULL';
        }

        foreach ($conditions as $column => $value) {
            $wheres[] = "$column = :$column";
            $params[$column] = $value;
        }

        if (!empty($wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $wheres);
        }

        return (int) Database::selectValue($sql, $params);
    }

    /**
     * Hydrate model from database row
     */
    protected static function hydrate(array $row): static
    {
        $model = new static();
        $model->attributes = $row;
        $model->original = $row;
        $model->exists = true;
        return $model;
    }

    /**
     * Create query builder for model
     */
    public static function query(): QueryBuilder
    {
        $builder = new QueryBuilder(static::getTable());
        
        if (static::$softDeletes && static::$deletedAt) {
            $builder->whereNull(static::$deletedAt);
        }

        return $builder;
    }

    /**
     * Refresh model from database
     */
    public function refresh(): self
    {
        $fresh = static::find($this->getKey());
        if ($fresh) {
            $this->attributes = $fresh->attributes;
            $this->original = $fresh->original;
        }
        return $this;
    }
}
