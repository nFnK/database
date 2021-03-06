<?php

namespace Odan\Database;

use PDO;

class Connection extends PDO
{

    /**
     * Quotes a value for use in a query.
     *
     * @param mixed $value
     * @return string|false a quoted string
     */
    public function quoteValue($value): string
    {
        if ($value === null) {
            return 'NULL';
        }
        return $this->quote($value);
    }

    /**
     * Quote array values.
     *
     * @param array|null $array
     * @return array
     */
    public function quoteArray(array $array): array
    {
        if (!$array) {
            return [];
        }
        foreach ($array as $key => $value) {
            $array[$key] = $this->quoteValue($value);
        }
        return $array;
    }

    /**
     * Escape identifier (column, table) with backticks
     *
     * @see: http://dev.mysql.com/doc/refman/5.0/en/identifiers.html
     *
     * @param string $identifier Identifier name
     * @return string Quoted identifier
     */
    public function quoteName(string $identifier): string
    {
        $identifier = trim($identifier);
        $separators = array(' AS ', ' ', '.');
        foreach ($separators as $sep) {
            $pos = strripos($identifier, $sep);
            if ($pos) {
                return $this->quoteNameWithSeparator($identifier, $sep, $pos);
            }
        }
        return $this->quoteIdentifier($identifier);
    }

    /**
     * Quote array of names.
     *
     * @param array $identifiers
     * @return array
     */
    public function quoteNames(array $identifiers): array
    {
        foreach ((array)$identifiers as $key => $identifier) {
            if ($identifier instanceof RawExp) {
                continue;
            }
            $identifiers[$key] = $this->quoteName($identifier);
        }
        return $identifiers;
    }

    /**
     * Quotes an identifier that has a separator.
     *
     * @param string $spec The identifier name to quote.
     * @param string $sep The separator, typically a dot or space.
     * @param int $pos The position of the separator.
     * @return string The quoted identifier name.
     */
    protected function quoteNameWithSeparator(string $spec, string $sep, int $pos): string
    {
        $len = strlen($sep);
        $part1 = $this->quoteName(substr($spec, 0, $pos));
        $part2 = $this->quoteIdentifier(substr($spec, $pos + $len));
        return "{$part1}{$sep}{$part2}";
    }

    /**
     * Quotes an identifier name (table, index, etc); ignores empty values and
     * values of '*'.
     *
     * Escape backticks inside by doubling them
     * Enclose identifier in backticks
     *
     * After such formatting, it is safe to insert the $table variable into query.
     *
     * @param string $name The identifier name to quote.
     * @return string The quoted identifier name.
     * @see quoteName()
     */
    public function quoteIdentifier(string $name): string
    {
        $name = trim($name);
        if ($name == '*') {
            return $name;
        }
        return "`" . str_replace("`", "``", $name) . "`";
    }

    /**
     * Retrieving a list of column values
     *
     * sample:
     * $lists = $db->queryValues('SELECT id FROM table;', 'id');
     *
     * @param string $sql
     * @param string $key
     * @return array
     */
    public function queryValues(string $sql, string $key): array
    {
        $result = [];
        $statement = $this->query($sql);
        while ($row = $statement->fetch()) {
            $result[] = $row[$key];
        }
        return $result;
    }

    /**
     * Retrieve only the given column of the first result row
     *
     * @param string $sql
     * @param string $column
     * @param mixed $default
     * @return mixed|null
     */
    public function queryValue(string $sql, string $column, $default = null)
    {
        $result = $default;
        if ($row = $this->query($sql)->fetch()) {
            $result = $row[$column];
        }
        return $result;
    }

    /**
     * Map query result by column as new index
     *
     * <code>
     * $rows = $db->queryMapColumn('SELECT * FROM table;', 'id');
     * </code>
     *
     * @param string $sql
     * @param string $key Column name to map as index
     * @return array
     */
    public function queryMapColumn(string $sql, string $key): array
    {
        $result = [];
        $statement = $this->query($sql);
        while ($row = $statement->fetch()) {
            $result[$row[$key]] = $row;
        }
        return $result;
    }
}
