<?php
/**
 * @package go\DB
 */

namespace go\DB\Helpers;

/**
 * The map of fields to columns (use in the Table class)
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class MapFields
{
    /**
     * The constructor
     *
     * @param array $map
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * @param string $col
     * @return string
     */
    public function col($col)
    {
        if (\is_string($col) && (isset($this->map[$col]))) {
            $col = $this->map[$col];
        }
        return $col;
    }

    /**
     * @param mixed $cols
     * @return mixed
     */
    public function cols($cols)
    {
        if (\is_array($cols)) {
            foreach ($cols as &$col) {
                if (\is_string($col) && (isset($this->map[$col]))) {
                    $col = $this->map[$col];
                }
            }
            unset($col);
        } elseif ((\is_string($cols) && (isset($this->map[$cols])))) {
            $cols = $this->map[$cols];
        }
        return $cols;
    }

    /**
     * @param mixed $set
     * @return mixed
     */
    public function set($set)
    {
        $result = array();
        foreach ($set as $k => $v) {
            if (isset($this->map[$k])) {
                $k = $this->map[$k];
            }
            if (isset($v['col']) && (isset($this->map[$v['col']]))) {
                $v['col'] = $this->map[$v['col']];
            }
            $result[$k] = $v;
        }
        return $result;
    }

    /**
     * @param mixed $where
     * @return mixed
     */
    public function where($where)
    {
        if (!\is_array($where)) {
            return $where;
        }
        $result = array();
        foreach ($where as $k => $v) {
            if (isset($this->map[$k])) {
                $k = $this->map[$k];
            }
            if (isset($v['col']) && (isset($this->map[$v['col']]))) {
                $v['col'] = $this->map[$v['col']];
            }
            $result[$k] = $v;
        }
        return $result;
    }

    /**
     * @param mixed $order
     * @return mixed
     */
    public function order($order)
    {
        if (\is_string($order)) {
            return isset($this->map[$order]) ? $this->map[$order] : $order;
        }
        if (!\is_array($order)) {
            return null;
        }
        $result = array();
        foreach ($order as $k => $v) {
            if (\is_int($k)) {
                $v = isset($this->map[$v]) ? $this->map[$v] : $v;
            } else {
                $k = isset($this->map[$k]) ? $this->map[$k] : $k;
            }
            $result[$k] = $v;
        }
        return $result;
    }

    /**
     * @param array $rows
     * @return array
     */
    public function assoc($rows)
    {
        if (!$this->flip) {
            $this->flip = \array_flip($this->map);
        }
        $result = array();
        foreach ($rows as $row) {
            $item = array();
            foreach ($row as $k => $v) {
                $k = isset($this->flip[$k]) ? $this->flip[$k] : $k;
                $item[$k] = $v;
            }
            $result[] = $item;
        }
        return $result;
    }

    /**
     * @param array $row
     * @return array
     */
    public function row($row)
    {
        if (!\is_array($row)) {
            return $row;
        }
        if (!$this->flip) {
            $this->flip = \array_flip($this->map);
        }
        $item = array();
        foreach ($row as $k => $v) {
            $k = isset($this->flip[$k]) ? $this->flip[$k] : $k;
            $item[$k] = $v;
        }
        return $item;
    }

    /**
     * @return array
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * @var array
     */
    protected $map;

    /**
     * @var array
     */
    protected $flip;
}
