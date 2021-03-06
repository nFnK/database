<?php

namespace Odan\Database;

use Closure;
use PDOStatement;

/**
 * Class SelectQuery
 */
class SelectQuery extends SelectQueryBuilder
{
    /**
     * Distinct
     *
     * @return self
     */
    public function distinct(): self
    {
        $this->distinct = true;
        return $this;
    }

    /**
     * Columns
     *
     * @param string|array $fields
     * @return self
     */
    public function columns($fields): self
    {
        $this->columns = (array)$fields;
        return $this;
    }

    /**
     * From
     *
     * @param string $table Table name
     * @return self
     */
    public function from(string $table): self
    {
        $this->from = $table;
        return $this;
    }

    /**
     * Join.
     *
     * @param string $table Table name
     * @param string $leftField Name of the left field
     * @param string $comparison Comparison (=,<,>,<=,>=,<>,in, not in, between, not between)
     * @param $rightField Value of the right field
     * @return self
     */
    public function join(string $table, string $leftField, string $comparison, $rightField): self
    {
        $this->join[] = ['inner', $table, $leftField, $comparison, $rightField];
        return $this;
    }

    /**
     * Left Join.
     *
     * @param string $table Table name
     * @param string $leftField Name of the left field
     * @param string $comparison Comparison (=,<,>,<=,>=,<>,in, not in, between, not between)
     * @param $rightField Value of the right field
     * @return self
     */
    public function leftJoin(string $table, string $leftField, string $comparison, $rightField): self
    {
        $this->join[] = ['left', $table, $leftField, $comparison, $rightField];
        return $this;
    }

    /**
     * Cross Join.
     *
     * @param string $table Table name
     * @param string $leftField Name of the left field
     * @param string $comparison Comparison (=,<,>,<=,>=,<>,in, not in, between, not between)
     * @param $rightField Value of the right field
     * @return self
     */
    public function crossJoin(string $table, string $leftField, string $comparison, $rightField): self
    {
        $this->join[] = ['cross', $table, $leftField, $comparison, $rightField];
        return $this;
    }

    /**
     * Where AND condition.
     *
     * @param array ...$conditions (field, comparison, value)
     * or (field, comparison, new RawExp('table.field'))
     * or new RawExp('...')
     * @return self
     */
    public function where(...$conditions): self
    {
        if ($conditions[0] instanceof Closure) {
            $this->addClauseCondClosure('where', 'AND', $conditions[0]);
            return $this;
        }
        $this->where[] = ['and', $conditions];
        return $this;
    }

    /**
     * Where OR condition.
     *
     * @param array ...$conditions (field, comparison, value)
     * or (field, comparison, new RawExp('table.field'))
     * or new RawExp('...')
     * @return self
     */
    public function orWhere(...$conditions): self
    {
        if ($conditions[0] instanceof Closure) {
            $this->addClauseCondClosure('where', 'OR', $conditions[0]);
            return $this;
        }
        $this->where[] = ['or', $conditions];
        return $this;
    }

    /**
     * Order by.
     *
     * @param array $fields Column name(s)
     * @return self
     */
    public function orderBy(array $fields): self
    {
        $this->orderBy = $fields;
        return $this;
    }

    /**
     * Group by.
     *
     * @param array $fields
     * @return self
     */
    public function groupBy(array $fields): self
    {
        $this->groupBy = $fields;
        return $this;
    }

    /**
     * Add AND having condition.
     *
     * @param array ...$conditions (field, comparison, value)
     * or (field, comparison, new RawExp('table.field'))
     * or new RawExp('...')
     * @return self
     */
    public function having(...$conditions): self
    {
        if ($conditions[0] instanceof Closure) {
            $this->addClauseCondClosure('having', 'AND', $conditions[0]);
            return $this;
        }
        $this->having[] = ['and', $conditions];
        return $this;
    }

    /**
     * Add OR having condition.
     *
     * @param array ...$conditions (field, comparison, value)
     * or (field, comparison, new RawExp('table.field'))
     * or new RawExp('...')
     * @return self
     */
    public function orHaving(...$conditions): self
    {
        if ($conditions[0] instanceof Closure) {
            $this->addClauseCondClosure('having', 'OR', $conditions[0]);
            return $this;
        }
        $this->having[] = ['or', $conditions];
        return $this;
    }

    /**
     * Limit the number of rows returned.
     *
     * @param float $rowCount Row count
     * @return self
     */
    public function limit(float $rowCount): self
    {
        $this->limit = $rowCount;
        return $this;
    }

    /**
     * Offset of the first row to return.
     *
     * @param float $offset Offset
     * @return self
     */
    public function offset(float $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object.
     *
     * @return PDOStatement
     */
    public function query(): PDOStatement
    {
        return $this->pdo->query($this->build());
    }

    /**
     * Prepares a statement for execution and returns a statement object
     *
     * @return PDOStatement
     */
    public function prepare(): PDOStatement
    {
        return $this->pdo->prepare($this->build());
    }
}
