<?php
/**
 * @package go\DB
 */

namespace go\DB;

use go\DB\Helpers\MapFields;
use go\DB\Helpers\Fetchers\Arr as ArrFetcher;

/**
 * Access to a specified table
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Table
{
    /**
     * The constructor
     *
     * @param \go\DB\DB $db
     *        the database of the table
     * @param string $tablename
     *        the table name
     * @param array $map [optional]
     *        a field map (a field => a real column name)
     */
    public function __construct(DB $db, $tablename, array $map = null)
    {
        $this->db = $db;
        $this->name = $tablename;
        if (!empty($map)) {
            $this->map = new MapFields($map);
        }
    }

    /**
     * Returns the table name
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->name;
    }

    /**
     * Returns a database of this table
     *
     * @return \go\DB\DB
     */
    public function getDB()
    {
        return $this->db;
    }

    /**
     * Inserts a row to the table
     *
     * @param array $set
     *        the data for set (a field => a value)
     * @return int
     *         ID of the row
     */
    public function insert(array $set)
    {
        if (empty($set)) {
            return null;
        }
        if ($this->map) {
            $set = $this->map->set($set);
        }
        $pattern = 'INSERT INTO ?t (?cols) VALUES (?ln)';
        $data = array($this->name, \array_keys($set), \array_values($set));
        return $this->db->query($pattern, $data)->id();
    }

    /**
     * Multi insert
     *
     * @param array $sets
     *        a list of the rows
     * @param bool $imp [optional]
     *        TRUE if multi-insert is implemented in this db
     */
    public function multiInsert(array $sets, $imp = true)
    {
        if (!$imp) {
            foreach ($sets as $set) {
                $this->insert($set);
            }
            return;
        }
        if (empty($sets)) {
            return;
        }
        if ($this->map) {
            foreach ($sets as &$set) {
                $set = $this->map->set($set);
            }
            unset($set);
        }
        if (isset($sets[0])) {
            $first = $sets[0];
        } else {
            $first = \current($sets);
        }
        $pattern = 'INSERT INTO ?t (?cols) VALUES ?vn';
        $data = array($this->name, \array_keys($first), $sets);
        $this->db->query($pattern, $data);
    }

    /**
     * Replaces or inserts a row
     *
     * @param array $set
     * @return int
     */
    public function replace(array $set)
    {
        if (empty($set)) {
            return null;
        }
        if ($this->map) {
            $set = $this->map->set($set);
        }
        $pattern = 'REPLACE INTO ?t (?cols) VALUES (?ln)';
        $data = array($this->name, \array_keys($set), \array_values($set));
        return $this->db->query($pattern, $data)->id();
    }

    /**
     * Multi replace
     *
     * @param array $sets
     * @param bool $imp
     *        TRUE if multi-insert is implemented in this db
     */
    public function multiReplace(array $sets, $imp = true)
    {
        if (!$imp) {
            foreach ($sets as $set) {
                $this->replace($set);
            }
            return;
        }
        if (empty($sets)) {
            return;
        }
        if ($this->map) {
            foreach ($sets as &$set) {
                $set = $this->map->set($set);
            }
            unset($set);
        }
        if (isset($sets[0])) {
            $first = $sets[0];
        } else {
            $first = \current($sets);
        }
        $pattern = 'REPLACE INTO ?t (?cols) VALUES ?vn';
        $data = array($this->name, \array_keys($first), $sets);
        $this->db->query($pattern, $data);
    }

    /**
     * Updates the table
     *
     * @param array $set
     * @param mixed $where [optional]
     * @return int
     *         number of affected rows
     */
    public function update(array $set, $where = true)
    {
        if (empty($set)) {
            return 0;
        }
        if ($this->map) {
            $set = $this->map->set($set);
            $where = $this->map->where($where);
        }
        $pattern = 'UPDATE ?t SET ?sn WHERE ?w';
        $data = array($this->name, $set, $where);
        return $this->db->query($pattern, $data)->ar();
    }

    /**
     * Selects rows from the table
     *
     * @param mixed $cols [optional]
     * @param mixed $where [optional]
     * @param mixed $order [optional]
     * @param mixed $limit [optional]
     * @return \go\DB\Result
     */
    public function select($cols = null, $where = true, $order = null, $limit = null)
    {
        if ($this->map) {
            $cols = $this->map->cols($cols);
            $where = $this->map->where($where);
            $order = $this->map->order($order);
        }
        if ($cols === null) {
            $cols = true;
        }
        $pattern = 'SELECT ?cols FROM ?t WHERE ?w';
        $data = array($cols, $this->name, $where);
        if (\is_array($order)) {
            if (!empty($order)) {
                $ords = array();
                foreach ($order as $k => $v) {
                    $ords[] = '?c '.($v ? 'ASC' : 'DESC');
                    $data[] = $k;
                }
                $pattern .= ' ORDER BY '.\implode(',', $ords);
            }
        } elseif ($order !== null) {
            $pattern .= ' ORDER BY ?c ASC';
            $data[] = $order;
        }
        if ($limit !== null) {
            if (\is_array($limit)) {
                $offset = isset($limit[1]) ? $limit[1] : 0;
                $limit = $limit[0];
            } else {
                $offset = 0;
            }
            $pattern .= ' LIMIT ?i,?i';
            $data[] = $offset;
            $data[] = $limit;
        }
        $fetcher = $this->db->query($pattern, $data);
        if ($this->map) {
            $data = $fetcher->assoc();
            $data = $this->map->assoc($data);
            $fetcher = new ArrFetcher($data);
        }
        return $fetcher;
    }

    /**
     * Deletes rows from the table
     *
     * @param mixed $where
     * @return int
     *         number of affected rows
     */
    public function delete($where = null)
    {
        $pattern = 'DELETE FROM ?t WHERE ?w';
        if ($this->map) {
            $where = $this->map->where($where);
        }
        $data = array($this->name, $where);
        return $this->db->query($pattern, $data)->ar();
    }

    /**
     * Truncates the table
     */
    public function truncate()
    {
        $pattern = 'TRUNCATE TABLE ?t';
        $data = array($this->name);
        $this->db->query($pattern, $data);
    }

    /**
     * Returns COUNT()
     *
     * @param mixed $col [optional]
     * @param mixed $where [optional]
     * @return int
     */
    public function getCount($col = null, $where = true)
    {
        if ($col !== null) {
            $p = '?c';
            if ($this->map) {
                $col = $this->map->col($col);
            }
        } else {
            $p = '?q';
            $col = 1;
        }
        if ($this->map) {
            $where = $this->map->where($where);
        }
        $pattern = 'SELECT COUNT('.$p.') FROM ?t WHERE ?w';
        $data = array($col, $this->name, $where);
        return $this->db->query($pattern, $data)->el();
    }

    /**
     * Returns a columns map object
     *
     * @return \go\DB\Helpers\MapFields
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * The table name
     *
     * @var string
     */
    private $name;

    /**
     * The database instance
     *
     * @var \go\DB\DB
     */
    private $db;

    /**
     * The field-to-column map
     *
     * @var \go\DB\Helpers\MapFields
     */
    private $map;
}
