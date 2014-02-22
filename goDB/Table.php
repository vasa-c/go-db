<?php
/**
 * @package    go\DB
 * @subpackage Storage
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB;

/**
 * Access to a specified table
 */
class Table
{
    /**
     * Constructor
     *
     * @param \go\DB\DB $db
     *        a database of the table
     * @param string $tablename
     *        the table name
     */
    public function __construct(\go\DB\DB $db, $tablename)
    {
        $this->db = $db;
        $this->name = $tablename;
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
     * Insert a row to the table
     *
     * @param array $set
     *        the data for set
     * @return int
     *         ID of the row
     */
    public function insert(array $set)
    {
        if (empty($set)) {
            return null;
        }
        $pattern = 'INSERT INTO ?t (?cols) VALUES (?ln)';
        $data = array($this->name, \array_keys($set), \array_values($set));
        return $this->db->query($pattern, $data)->id();
    }

    /**
     * Multi insert
     *
     * @param array $sets
     *        a list of rows
     * @param bool $imp [optional]
     *        multi insert is implemented in this db
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
     * Replace a row
     *
     * @param array $set
     */
    public function replace(array $set)
    {
        if (empty($set)) {
            return null;
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
     * Update the table
     *
     * @param array $set
     * @param mixed $where [optional]
     * @return int
     *         affected rows
     */
    public function update(array $set, $where = true)
    {
        if (empty($set)) {
            return 0;
        }
        $pattern = 'UPDATE ?t SET ?sn WHERE ?w';
        $data = array($this->name, $set, $where);
        return $this->db->query($pattern, $data)->ar();
    }

    /**
     * Select from the table
     *
     * @param mixed $cols [optional]
     * @param mixed $where [optional]
     * @param mixed $order [optional]
     * @param mixed $limit [optional]
     * @param mixed $offset [optional]
     * @return \go\DB\Result
     */
    public function select($cols = null, $where = true, $order = null, $limit = null, $offset = null)
    {
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
        if ($limit) {
            $pattern .= ' LIMIT ?i,?i';
            $data[] = $offset ?: 0;
            $data[] = $limit;
        }
        return $this->db->query($pattern, $data);
    }

    /**
     * Delete from the table
     *
     * @param mixed $where
     * @return int
     *         affected rows
     */
    public function delete($where = null)
    {
        $pattern = 'DELETE FROM ?t WHERE ?w';
        $data = array($this->name, $where);
        return $this->db->query($pattern, $data)->ar();
    }

    /**
     * Truncate the table
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
        } else {
            $p = '?q';
            $col = 1;
        }
        $pattern = 'SELECT COUNT('.$p.') FROM ?t WHERE ?w';
        $data = array($col, $this->name, $where);
        return $this->db->query($pattern, $data)->el();
    }

    /**
     * @var string
     */
    private $name;

    /**
     * @var \go\DB\DB
     */
    private $db;
}
