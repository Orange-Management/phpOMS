<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   phpOMS\DataStorage\Database\Query
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\DataStorage\Database\Query;

use phpOMS\Algorithm\Graph\DependencyResolver;
use phpOMS\DataStorage\Database\BuilderAbstract;
use phpOMS\DataStorage\Database\Connection\ConnectionAbstract;

/**
 * Database query builder.
 *
 * @package phpOMS\DataStorage\Database\Query
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 *
 * @todo Orange-Management/phpOMS#33
 *  Implement missing grammar & builder functions
 *  Missing elements are e.g. sum, merge etc.
 */
class Builder extends BuilderAbstract
{
    /**
     * Is read only.
     *
     * @var bool
     * @since 1.0.0
     */
    protected bool $isReadOnly = false;

    /**
     * Columns.
     *
     * @var array
     * @since 1.0.0
     */
    public array $selects = [];

    /**
     * Columns.
     *
     * @var array
     * @since 1.0.0
     */
    public array $random;

    /**
     * Columns.
     *
     * @var array
     * @since 1.0.0
     */
    public array $updates = [];

    /**
     * Stupid work around because value needs to be not null for it to work in Grammar.
     *
     * @var array
     * @since 1.0.0
     */
    public array $deletes = [1];

    /**
     * Into.
     *
     * @var string
     * @since 1.0.0
     */
    public string $into = '';

    /**
     * Into columns.
     *
     * @var array
     * @since 1.0.0
     */
    public array $inserts = [];

    /**
     * Into columns.
     *
     * @var array<int, mixed>
     * @since 1.0.0
     */
    public array $values = [];

    /**
     * Into columns.
     *
     * @var array
     * @since 1.0.0
     */
    public array $sets = [];

    /**
     * Distinct.
     *
     * @var bool
     * @since 1.0.0
     */
    public bool $distinct = false;

    /**
     * From.
     *
     * @var array
     * @since 1.0.0
     */
    public array $from = [];

    /**
     * Joins.
     *
     * @var array
     * @since 1.0.0
     */
    public array $joins = [];

    /**
     * Ons of joins.
     *
     * @var array
     * @since 1.0.0
     */
    public array $ons = [];

    /**
     * Where.
     *
     * @var array
     * @since 1.0.0
     */
    public array $wheres = [];

    /**
     * Group.
     *
     * @var string[]
     * @since 1.0.0
     */
    public array $groups = [];

    /**
     * Order.
     *
     * @var array
     * @since 1.0.0
     */
    public array $orders = [];

    /**
     * Limit.
     *
     * @var null|int
     * @since 1.0.0
     */
    public ?int $limit = null;

    /**
     * Offset.
     *
     * @var null|int
     * @since 1.0.0
     */
    public ?int $offset = null;

    /**
     * Binds.
     *
     * @var array
     * @since 1.0.0
     */
    private array $binds = [];

    /**
     * Union.
     *
     * @var array
     * @since 1.0.0
     */
    public array $unions = [];

    /**
     * Lock.
     *
     * @var bool
     * @since 1.0.0
     */
    public bool $lock = false;

    /**
     * Comparison OPERATORS.
     *
     * @var string[]
     * @since 1.0.0
     */
    public const OPERATORS = [
        '=',
        '<',
        '>',
        '<=',
        '>=',
        '<>',
        '!=',
        'like',
        'like binary',
        'not like',
        'between',
        'ilike',
        '&',
        '|',
        '^',
        '<<',
        '>>',
        'rlike',
        'regexp',
        'not regexp',
        '~',
        '~*',
        '!~',
        '!~*',
        'similar to',
        'not similar to',
        'in',
    ];

    /**
     * Constructor.
     *
     * @param ConnectionAbstract $connection Database connection
     * @param bool               $readOnly   Query is read only
     *
     * @since 1.0.0
     */
    public function __construct(ConnectionAbstract $connection, bool $readOnly = false)
    {
        $this->isReadOnly = $readOnly;
        $this->setConnection($connection);
    }

    /**
     * Set connection for grammar.
     *
     * @param ConnectionAbstract $connection Database connection
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setConnection(ConnectionAbstract $connection) : void
    {
        $this->connection = $connection;
        $this->grammar    = $connection->getGrammar();
    }

    /**
     * Select.
     *
     * @param array ...$columns Columns
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function select(...$columns) : self
    {
        $this->type = QueryType::SELECT;

        foreach ($columns as $key => $column) {
            if (\is_string($column) || $column instanceof self) {
                $this->selects[] = $column;
            } else {
                throw new \InvalidArgumentException();
            }
        }

        return $this;
    }

    /**
     * Select with alias.
     *
     * @param mixed  $column Column query
     * @param string $alias  Alias
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function selectAs($column, string $alias) : self
    {
        $this->type            = QueryType::SELECT;
        $this->selects[$alias] = $column;

        return $this;
    }

    /**
     * Select.
     *
     * @param array ...$columns Columns
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function random(...$columns) : self
    {
        $this->select(...$columns);

        $this->type   = QueryType::RANDOM;
        $this->random = &$this->selects;

        return $this;
    }

    /**
     * Bind parameter.
     *
     * @param mixed $binds Binds
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function bind($binds) : self
    {
        if (\is_array($binds)) {
            $this->binds += $binds;
        } elseif (\is_string($binds)) {
            $this->binds[] = $binds;
        } else {
            throw new \InvalidArgumentException();
        }

        return $this;
    }

    /**
     * Creating new.
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function newQuery() : self
    {
        return new self($this->connection, $this->isReadOnly);
    }

    /**
     * Parsing to sql string.
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function toSql() : string
    {
        if (!empty($this->joins)) {
            $this->resolveJoinDependencies();
        }

        return $this->grammar->compileQuery($this);
    }

    /**
     * Resolves join dependencies
     *
     * @return void
     *
     * @since 1.0.0
     */
    private function resolveJoinDependencies() : void
    {
        // create dependencies
        $dependencies = [];
        foreach ($this->joins as $table => $join) {
            $dependencies[$table] = [];

            foreach ($this->ons[$table] as $on) {
                if (\stripos($on['column'], '.')) {
                    $dependencies[$table][] = \explode('.', $on['column'])[0];
                }

                if (\stripos($on['value'], '.')) {
                    $dependencies[$table][] = \explode('.', $on['value'])[0];
                }
            }
        }

        // add from to existing dependencies
        foreach ($this->from as $table => $from) {
            $dependencies[$table] = [];
        }

        $resolved = DependencyResolver::resolve($dependencies);

        // cyclomatic dependencies
        if ($resolved === null) {
            return;
        }

        $temp        = $this->joins;
        $this->joins = [];
        foreach ($resolved as $table) {
            if (isset($temp[$table])) {
                $this->joins[$table] = $temp[$table];
            }
        }
    }

    /**
     * Set raw query.
     *
     * @param string $raw Raw query
     *
     * @return Builder
     *
     * @throws \Exception
     *
     * @since 1.0.0
     */
    public function raw(string $raw) : self
    {
        if (!$this->isValidReadOnly($raw)) {
            throw new \Exception();
        }

        $this->type = QueryType::RAW;
        $this->raw  = \rtrim($raw, ';');

        return $this;
    }

    /**
     * Tests if a string contains a non read only component in case the builder is read only.
     * If the builder is not read only it will always return true
     *
     * @param string $raw Raw query
     *
     * @return bool
     *
     * @since 1.0.0
     */
    private function isValidReadOnly(string $raw) : bool
    {
        if (!$this->isReadOnly) {
            return true;
        }

        $raw = \strtolower($raw);

        if (\stripos($raw, 'insert') !== false
            || \stripos($raw, 'update') !== false
            || \stripos($raw, 'drop') !== false
            || \stripos($raw, 'delete') !== false
            || \stripos($raw, 'create') !== false
            || \stripos($raw, 'alter') !== false
        ) {
            return false;
        }

        return true;
    }

    /**
     * Is distinct.
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function distinct() : self
    {
        $this->distinct = true;

        return $this;
    }

    /**
     * From.
     *
     * @param array ...$tables Tables
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function from(...$tables) : self
    {
        foreach ($tables as $key => $table) {
            if (\is_string($table) || $table instanceof self) {
                $this->from[] = $table;
            } else {
                throw new \InvalidArgumentException();
            }
        }

        return $this;
    }

    /**
     * From with alias.
     *
     * @param mixed  $column Column query
     * @param string $alias  Alias
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function fromAs($column, string $alias) : self
    {
        $this->from[$alias] = $column;

        return $this;
    }

    /**
     * Where.
     *
     * @param array|string|Where $columns  Columns
     * @param array|string       $operator Operator
     * @param mixed              $values   Values
     * @param array|string       $boolean  Boolean condition
     *
     * @return Builder
     *
     * @throws \InvalidArgumentException
     *
     * @since 1.0.0
     */
    public function where($columns, $operator = null, $values = null, $boolean = 'and') : self
    {
        if (!\is_array($columns)) {
            $columns  = [$columns];
            $operator = [$operator];
            $values   = [$values];
            $boolean  = [$boolean];
        }

        $i = 0;
        foreach ($columns as $column) {
            if (isset($operator[$i]) && !\in_array(\strtolower($operator[$i]), self::OPERATORS)) {
                throw new \InvalidArgumentException('Unknown operator.');
            }

            $this->wheres[self::getPublicColumnName($column)][] = [
                'column'   => $column,
                'operator' => $operator[$i],
                'value'    => $values[$i],
                'boolean'  => $boolean[$i],
            ];

            ++$i;
        }

        return $this;
    }

    /**
     * Get column of where condition
     *
     * One column can have multiple where conditions.
     *
     * @param mixed $column Column
     *
     * @return null|array
     *
     * @since 1.0.0
     */
    public function getWhereByColumn($column) : ?array
    {
        return $this->wheres[self::getPublicColumnName($column)] ?? null;
    }

    /**
     * Where and sub condition.
     *
     * @param array|string|Where $where    Where sub condition
     * @param mixed              $operator Operator
     * @param mixed              $values   Values
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function andWhere($where, $operator = null, $values = null) : self
    {
        return $this->where($where, $operator, $values, 'and');
    }

    /**
     * Where or sub condition.
     *
     * @param array|string|Where $where    Where sub condition
     * @param mixed              $operator Operator
     * @param mixed              $values   Values
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function orWhere($where, $operator = null, $values = null) : self
    {
        return $this->where($where, $operator, $values, 'or');
    }

    /**
     * Where in.
     *
     * @param array|string|Where $column  Column
     * @param mixed              $values  Values
     * @param string             $boolean Boolean condition
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function whereIn($column, $values = null, string $boolean = 'and') : self
    {
        $this->where($column, 'in', $values, $boolean);

        return $this;
    }

    /**
     * Where null.
     *
     * @param array|string|Where $column  Column
     * @param string             $boolean Boolean condition
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function whereNull($column, string $boolean = 'and') : self
    {
        $this->where($column, '=', null, $boolean);

        return $this;
    }

    /**
     * Where not null.
     *
     * @param array|string|Where $column  Column
     * @param string             $boolean Boolean condition
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function whereNotNull($column, string $boolean = 'and') : self
    {
        $this->where($column, '!=', null, $boolean);

        return $this;
    }

    /**
     * Group by.
     *
     * @param array|string ...$columns Grouping result
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function groupBy(...$columns) : self
    {
        foreach ($columns as $key => $column) {
            if (\is_string($column) || $column instanceof self) {
                $this->groups[] = $column;
            } else {
                throw new \InvalidArgumentException();
            }
        }

        return $this;
    }

    /**
     * Order by newest.
     *
     * @param string $column Column
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function newest(string $column) : self
    {
        $this->orderBy($column, 'DESC');

        return $this;
    }

    /**
     * Order by oldest.
     *
     * @param string $column Column
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function oldest(string $column) : self
    {
        $this->orderBy($column, 'ASC');

        return $this;
    }

    /**
     * Order by oldest.
     *
     * @param array|string    $columns Columns
     * @param string|string[] $order   Orders
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function orderBy($columns, $order = 'DESC') : self
    {
        if (\is_string($columns)) {
            if (!\is_string($order)) {
                throw new \InvalidArgumentException();
            }

            if (!isset($this->orders[$order])) {
                $this->orders[$order] = [];
            }

            $this->orders[$order][] = $columns;
        } elseif (\is_array($columns)) {
            foreach ($columns as $key => $column) {
                $this->orders[\is_string($order) ? $order : $order[$key]][] = $column;
            }
        } else {
            throw new \InvalidArgumentException();
        }

        return $this;
    }

    /**
     * Offset.
     *
     * @param int $offset Offset
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function offset(int $offset) : self
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Limit.
     *
     * @param int $limit Limit
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function limit(int $limit) : self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Union.
     *
     * @param mixed $query Query
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function union($query) : self
    {
        if (!\is_array($query)) {
            $this->unions[] = $query;
        } else {
            $this->unions += $query;
        }

        return $this;
    }

    /**
     * Lock query.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function lock() : void
    {
    }

    /**
     * Lock for update query.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function lockUpdate() : void
    {
    }

    /**
     * Create query string.
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function __toString()
    {
        return $this->grammar->compileQuery($this);
    }

    /**
     * Find query.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function find() : void
    {
    }

    /**
     * Count results.
     *
     * @param string $table Table to count the result set
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function count(string $table = '*') : self
    {
        // todo: don't do this as string, create new object new \count(); this can get handled by the grammar parser WAY better
        return $this->select('COUNT(' . $table . ')');
    }

    /**
     * Select minimum.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function min() : void
    {
    }

    /**
     * Select maximum.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function max() : void
    {
    }

    /**
     * Select sum.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function sum() : void
    {
    }

    /**
     * Select average.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function avg() : void
    {
    }

    /**
     * Insert into columns.
     *
     * @param array ...$columns Columns
     *
     * @return Builder
     *
     * @throws \Exception
     *
     * @since 1.0.0
     */
    public function insert(...$columns) : self
    {
        if ($this->isReadOnly) {
            throw new \Exception();
        }

        $this->type = QueryType::INSERT;

        foreach ($columns as $column) {
            $this->inserts[] = $column;
        }

        return $this;
    }

    /**
     * Table to insert into.
     *
     * @param string $table Table
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function into(string $table) : self
    {
        $this->into = $table;

        return $this;
    }

    /**
     * Values to insert.
     *
     * @param array ...$values Values
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function values(...$values) : self
    {
        $this->values[] = $values;

        return $this;
    }

    /**
     * Get insert values
     *
     * @return array
     *
     * @since 1.0.0
     */
    public function getValues() : array
    {
        return $this->values;
    }

    /**
     * Values to insert.
     *
     * @param mixed $value Values
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function value($value) : self
    {
        \end($this->values);

        $key   = \key($this->values);
        $key ??= 0;

        if (\is_array($value)) {
            $this->values[$key + 1] = $value;
        } else {
            $this->values[$key][] = $value;
        }

        \reset($this->values);

        return $this;
    }

    /**
     * Values to insert.
     *
     * @param array ...$sets Values
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function sets(...$sets) : self
    {
        $this->sets[$sets[0]] = $sets[1] ?? null;

        return $this;
    }

    /**
     * Values to insert.
     *
     * @param mixed $set Values
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function set($set) : self
    {
        $this->sets[\key($set)] = \current($set);

        return $this;
    }

    /**
     * Update columns.
     *
     * @param array ...$tables Column names to update
     *
     * @return Builder
     *
     * @throws \Exception
     *
     * @since 1.0.0
     */
    public function update(...$tables) : self
    {
        if ($this->isReadOnly) {
            throw new \Exception();
        }

        $this->type = QueryType::UPDATE;

        foreach ($tables as $key => $table) {
            if (\is_string($table) || $table instanceof self) {
                $this->updates[] = $table;
            } else {
                throw new \InvalidArgumentException();
            }
        }

        return $this;
    }

    /**
     * Delete query
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function delete() : self
    {
        if ($this->isReadOnly) {
            throw new \Exception();
        }

        $this->type = QueryType::DELETE;

        return $this;
    }

    /**
     * Increment value.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function increment() : void
    {
    }

    /**
     * Decrement value.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function decrement() : void
    {
    }

    /**
     * Join.
     *
     * @param mixed       $table Join query
     * @param string      $type  Join type
     * @param null|string $alias Alias name (empty = none)
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function join($table, string $type = JoinType::JOIN, string $alias = null) : self
    {
        if (!\is_string($table)&& !($table instanceof self)) {
            throw new \InvalidArgumentException();
        }

        $this->joins[$alias ?? $table] = ['type' => $type, 'table' => $table, 'alias' => $alias];

        return $this;
    }

    /**
     * Join.
     *
     * @param mixed       $column Join query
     * @param null|string $alias  Alias name (empty = none)
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function leftJoin($column, string $alias = null) : self
    {
        return $this->join($column, JoinType::LEFT_JOIN, $alias);
    }

    /**
     * Join.
     *
     * @param mixed       $column Join query
     * @param null|string $alias  Alias name (empty = none)
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function leftOuterJoin($column, string $alias = null) : self
    {
        return $this->join($column, JoinType::LEFT_OUTER_JOIN, $alias);
    }

    /**
     * Join.
     *
     * @param mixed       $column Join query
     * @param null|string $alias  Alias name (empty = none)
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function leftInnerJoin($column, string $alias = null) : self
    {
        return $this->join($column, JoinType::LEFT_INNER_JOIN, $alias);
    }

    /**
     * Join.
     *
     * @param mixed       $column Join query
     * @param null|string $alias  Alias name (empty = none)
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function rightJoin($column, string $alias = null) : self
    {
        return $this->join($column, JoinType::RIGHT_JOIN, $alias);
    }

    /**
     * Join.
     *
     * @param mixed       $column Join query
     * @param null|string $alias  Alias name (empty = none)
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function rightOuterJoin($column, string $alias = null) : self
    {
        return $this->join($column, JoinType::RIGHT_OUTER_JOIN, $alias);
    }

    /**
     * Join.
     *
     * @param mixed       $column Join query
     * @param null|string $alias  Alias name (empty = none)
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function rightInnerJoin($column, string $alias = null) : self
    {
        return $this->join($column, JoinType::RIGHT_INNER_JOIN, $alias);
    }

    /**
     * Join.
     *
     * @param mixed       $column Join query
     * @param null|string $alias  Alias name (empty = none)
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function outerJoin($column, string $alias = null) : self
    {
        return $this->join($column, JoinType::OUTER_JOIN, $alias);
    }

    /**
     * Join.
     *
     * @param mixed       $column Join query
     * @param null|string $alias  Alias name (empty = none)
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function innerJoin($column, string $alias = null) : self
    {
        return $this->join($column, JoinType::INNER_JOIN, $alias);
    }

    /**
     * Join.
     *
     * @param mixed       $column Join query
     * @param null|string $alias  Alias name (empty = none)
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function crossJoin($column, string $alias = null) : self
    {
        return $this->join($column, JoinType::CROSS_JOIN, $alias);
    }

    /**
     * Join.
     *
     * @param mixed       $column Join query
     * @param null|string $alias  Alias name (empty = none)
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function fullJoin($column, string $alias = null) : self
    {
        return $this->join($column, JoinType::FULL_JOIN, $alias);
    }

    /**
     * Join.
     *
     * @param mixed       $column Join query
     * @param null|string $alias  Alias name (empty = none)
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function fullOuterJoin($column, string $alias = null) : self
    {
        return $this->join($column, JoinType::FULL_OUTER_JOIN, $alias);
    }

    /**
     * Rollback.
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function rollback() : self
    {
        return $this;
    }

    /**
     * On.
     *
     * @param string|array      $columns  Columns to join on
     * @param null|string|array $operator Comparison operator
     * @param null|string|array $values   Values to compare with
     * @param string|array      $boolean  Concatonator
     * @param null|string       $table    Table this belongs to
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function on($columns, $operator = null, $values = null, $boolean = 'and', string $table = null) : self
    {
        if ($operator !== null && !\is_array($operator) && !\in_array(\strtolower($operator), self::OPERATORS)) {
            throw new \InvalidArgumentException('Unknown operator.');
        }

        if (!\is_array($columns)) {
            $columns  = [$columns];
            $operator = [$operator];
            $values   = [$values];
            $boolean  = [$boolean];
        }

        $joinCount = \count($this->joins) - 1;
        $i         = 0;
        $table   ??= \array_keys($this->joins)[$joinCount];

        foreach ($columns as $column) {
            if (isset($operator[$i]) && !\in_array(\strtolower($operator[$i]), self::OPERATORS)) {
                throw new \InvalidArgumentException('Unknown operator.');
            }

            $this->ons[$table][] = [
                'column'   => $column,
                'operator' => $operator[$i],
                'value'    => $values[$i],
                'boolean'  => $boolean[$i],
            ];

            ++$i;
        }

        return $this;
    }

    /**
     * On.
     *
     * @param string|array      $columns  Columns to join on
     * @param null|string|array $operator Comparison operator
     * @param null|string|array $values   Values to compare with
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function orOn($columns, $operator = null, $values = null) : self
    {
        return $this->on($columns, $operator, $values, 'or');
    }

    /**
     * On.
     *
     * @param string|array      $columns  Columns to join on
     * @param null|string|array $operator Comparison operator
     * @param null|string|array $values   Values to compare with
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function andOn($columns, $operator = null, $values = null) : self
    {
        return $this->on($columns, $operator, $values, 'and');
    }

    /**
     * Merging query.
     *
     * Merging query in order to remove database query volume
     *
     * @param Builder $query Query
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function merge(self $query) : self
    {
        return clone($this);
    }

    /**
     * Execute query.
     *
     * @return mixed
     *
     * @since 1.0.0
     */
    public function execute()
    {
        $sth = $this->connection->con->prepare($this->toSql());

        foreach ($this->binds as $key => $bind) {
            $type = self::getBindParamType($bind);

            $sth->bindParam($key, $bind, $type);
        }

        $sth->execute();

        return $sth;
    }

    /**
     * Get bind parameter type.
     *
     * @param mixed $value Value to bind
     *
     * @return int
     *
     * @throws \Exception
     *
     * @since 1.0.0
     */
    public static function getBindParamType($value) : int
    {
        if (\is_int($value)) {
            return \PDO::PARAM_INT;
        } elseif (\is_string($value) || \is_float($value)) {
            return \PDO::PARAM_STR;
        }

        throw new \Exception();
    }

    /**
     * Get column name
     *
     * @param mixed $column Column name
     *
     * @return string
     *
     * @throws \Exception
     *
     * @since 1.0.0
     */
    public static function getPublicColumnName($column) : string
    {
        if (\is_string($column)) {
            return $column;
        } elseif ($column instanceof Column) {
            return $column->getColumn();
        } elseif ($column instanceof \Serializable) {
            return $column->serialize();
        }

        throw new \Exception();
    }
}
