<?php
/**
 * @package go\DB
 */

namespace go\DB\Fakes\Helpers;

/**
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class FakeQueries
{
    /**
     * @param array $data
     * @param mixed $where
     * @return array
     */
    public static function where($data, $where)
    {
        if (!is_array($where)) {
            if ($where === false) {
                return array();
            }
            return $data;
        }
        $result = [];
        foreach ($data as $k => $row) {
            if (self::rowWhere($row, $where)) {
                $result[$k] = $row;
            }
        }
        return $result;
    }

    /**
     * @param array $data
     * @param mixed $cols
     * @return array
     */
    public static function cols($data, $cols)
    {
        if (!is_array($cols)) {
            if (!is_string($cols)) {
                return $data;
            }
            $cols = array($cols);
        }
        $result = array();
        foreach ($data as $row) {
            $r = array();
            foreach ($cols as $k) {
                $r[$k] = isset($row[$k]) ? $row[$k] : null;
            }
            $result[] = $r;
        }
        return $result;
    }

    /**
     * @param array $data
     * @param mixed $order
     * @return array
     */
    public static function order($data, $order)
    {
        if ($order === null) {
            return $data;
        }
        if (!is_array($order)) {
            $order = [$order => true];
        }
        $sort = new Sort($order);
        return $sort->run($data);
    }

    /**
     * @param array $data
     * @param mixed $limit
     * @return array
     */
    public static function limit($data, $limit)
    {
        if ($limit === null) {
            return $data;
        }
        if (!is_array($limit)) {
            $limit = [0, $limit];
        }
        return array_slice($data, $limit[0], $limit[1]);
    }

    /**
     * @param array $row
     * @param bool $where
     * @return bool
     */
    private static function rowWhere($row, $where)
    {
        foreach ($where as $k => $w) {
            if (!isset($row[$k])) {
                if ($w !== null) {
                    return false;
                }
                continue;
            }
            $v = $row[$k];
            if (is_array($w)) {
                if (!in_array($v, $w)) {
                    return false;
                }
            } elseif ($w === true) {
                if ($v === null) {
                    return false;
                }
            } elseif ((string)$v !== (string)$w) {
                return false;
            }
        }
        return true;
    }
}
