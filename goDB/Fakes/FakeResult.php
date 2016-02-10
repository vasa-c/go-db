<?php
/**
 * @package go\DB
 */

namespace go\DB\Fakes;

use go\DB\Result;

/**
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class FakeResult implements Result
{
    /**
     * @param array $rows [optional]
     * @param int $id
     * @param int $ar
     */
    public function __construct(array $rows = null, $id = null, $ar = null)
    {
        $this->rows = $rows;
        $this->id = $id;
        $this->ar = $ar;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($fetch)
    {
        $m = explode(':', $fetch, 2);
        $param = isset($m[1]) ? $m[1] : null;
        $method = $m[0];
        return $this->$method($param);
    }

    /**
     * {@inheritdoc}
     */
    public function free()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function assoc($param = null)
    {
        return $this->paramKeys($this->rows, $param);
    }

    /**
     * {@inheritdoc}
     */
    public function numerics($param = null)
    {
        $result = array();
        foreach ($this->rows as $r) {
            $result[] = array_values($r);
        }
        return $this->paramKeys($result, $param);
    }

    /**
     * {@inheritdoc}
     */
    public function objects($param = null)
    {
        $result = $this->assoc($param);
        foreach ($result as &$r) {
            $r = (object)$r;
        }
        unset($r);
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function col($param = null)
    {
        $result = array();
        foreach ($this->rows as $r) {
            if ($param !== null) {
                $v = $r[$param];
            } else {
                $v = array_values($r);
                $v = $v[0];
            }
            $result[] = $v;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function vars($param = null)
    {
        $result = [];
        foreach ($this->rows as $r) {
            $v = array_values($r);
            $result[$v[0]] = isset($v[1]) ? $v[1] : $v[0];
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function iassoc($param = null)
    {
        return new \ArrayIterator($this->assoc($param));
    }

    /**
     * {@inheritdoc}
     */
    public function inumerics($param = null)
    {
        return new \ArrayIterator($this->numerics($param));
    }

    /**
     * {@inheritdoc}
     */
    public function iobjects($param = null)
    {
        return new \ArrayIterator($this->objects($param));
    }

    /**
     * {@inheritdoc}
     */
    public function ivars($param = null)
    {
        return new \ArrayIterator($this->vars($param));
    }

    /**
     * {@inheritdoc}
     */
    public function icol($param = null)
    {
        return new \ArrayIterator($this->col($param));
    }

    /**
     * {@inheritdoc}
     */
    public function row($param = null)
    {
        if (empty($this->rows)) {
            return null;
        }
        return current($this->rows);
    }

    /**
     * {@inheritdoc}
     */
    public function numeric($param = null)
    {
        if (empty($this->rows)) {
            return null;
        }
        return array_values(current($this->rows));
    }

    /**
     * {@inheritdoc}
     */
    public function object($param = null)
    {
        return (object)current($this->rows);
    }

    /**
     * {@inheritdoc}
     */
    public function el($param = null)
    {
        if (empty($this->rows)) {
            return null;
        }
        $r = array_values(current($this->rows));
        return isset($r[0]) ? $r[0] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function bool($param = null)
    {
        return (bool)$this->el($param);
    }

    /**
     * {@inheritdoc}
     */
    public function num($param = null)
    {
        return count($this->rows);
    }

    /**
     * {@inheritdoc}
     */
    public function id($param = null)
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function ar($param = null)
    {
        return $this->ar;
    }

    /**
     * {@inheritdoc}
     */
    public function cursor($param = null)
    {
        return $this->rows;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return $this->iassoc();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->rows);
    }

    /**
     * @param array $data
     * @param mixed $param
     * @return array
     */
    private function paramKeys($data, $param)
    {
        if ($param === null) {
            return $data;
        }
        $result = array();
        foreach ($data as $r) {
            $result[$r[$param]] = $r;
        }
        return $result;
    }

    /**
     * @var array
     */
    private $rows;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $ar;
}
