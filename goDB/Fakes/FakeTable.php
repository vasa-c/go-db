<?php
/**
 * @package go\DB
 */

namespace go\DB\Fakes;

use go\DB\Fakes\Helpers\FakeQueries;

/**
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class FakeTable implements IFakeTable
{
    /**
     * FakeTable constructor.
     * @param array $data
     * @param array $defaults [optional]
     * @param string|string[] $pk [optional]
     * @param int $lastAI [optional]
     */
    public function __construct(array $data, array $defaults = null, $pk = null, $lastAI = null)
    {
        $this->data = $data;
        if ($defaults === null) {
            $defaults = array();
        }
        $this->defaults = $defaults;
        $this->pk = $pk;
        $this->lastAI = $lastAI;
    }

    /**
     * {@inheritdoc}
     */
    public function insert(array $set)
    {
        $set = array_replace($this->defaults, $set);
        if ($this->lastAI !== null) {
            if (!isset($set[$this->pk])) {
                $this->lastAI++;
                $set[$this->pk] = $this->lastAI;
            } else {
                $this->lastAI = max($this->lastAI, $set[$this->pk]);
            }
        }
        $this->data[] = $set;
        $this->checkPK();
        return $this->lastAI;
    }

    /**
     * {@inheritdoc}
     */
    public function multiInsert(array $sets)
    {
        foreach ($sets as $set) {
            $this->insert($set);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $set)
    {
        $set = array_replace($this->defaults, $set);
        $cur = $this->findByPK($set);
        if ($cur === null) {
            return $this->insert($set);
        }
        $this->data[$cur] = $set;
    }

    /**
     * {@inheritdoc}
     */
    public function multiReplace(array $sets)
    {
        foreach ($sets as $set) {
            $this->replace($set);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $set, $where)
    {
        $rows = FakeQueries::where($this->data, $where);
        $ar = 0;
        foreach ($rows as $k => $row) {
            $nRow = array_replace($row, $set);
            if ($nRow !== $row) {
                $this->data[$k] = $nRow;
                $ar++;
            }
        }
        return $ar;
    }

    /**
     * {@inheritdoc}
     */
    public function select($cols, $where, $order, $limit)
    {
        $rows = array_values(FakeQueries::where($this->data, $where));
        $rows = FakeQueries::order($rows, $order);
        $rows = FakeQueries::cols($rows, $cols);
        $rows = FakeQueries::limit($rows, $limit);
        return new FakeResult($rows);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($where)
    {
        $del = FakeQueries::where($this->data, $where);
        if (empty($del)) {
            return 0;
        }
        foreach ($del as $k => $v) {
            unset($this->data[$k]);
        }
        $this->data = array_values($this->data);
        return count($del);
    }

    /**
     * {@inheritdoc}
     */
    public function truncate()
    {
        $this->data = array();
        if ($this->lastAI !== null) {
            $this->lastAI = 0;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCount($col, $where)
    {
        $rows = FakeQueries::where($this->data, $where);
        if (($col === null) || ($col === true)) {
            return count($rows);
        }
        $count = 0;
        foreach ($rows as $r) {
            if ($r[$col] !== null) {
                $count += 1;
            }
        }
        return $count;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getLastIncrement()
    {
        return $this->lastAI;
    }

    /**
     * @throws \LogicException
     */
    private function checkPK()
    {
        $pk = $this->pk;
        if (!$pk) {
            return;
        }
        if (!is_array($pk)) {
            $pk = [$pk];
        }
        $keys = [];
        foreach ($this->data as $row) {
            $key = [];
            foreach ($pk as $k) {
                $key[] = $row[$k];
            }
            $s = serialize($key);
            if (isset($keys[$s])) {
                throw new \LogicException('FakeTable duplicate PK '.implode('-', $key));
            }
            $keys[$s] = true;
        }
    }

    /**
     * @param array $row
     * @return int
     *         array index or NULL
     */
    private function findByPK(array $row)
    {
        $pk = $this->pk;
        if (!$pk) {
            return;
        }
        if (!is_array($pk)) {
            $pk = array($pk);
        }
        $where = array();
        foreach ($pk as $k) {
            $where[$k] = $row[$k];
        }
        $rows = FakeQueries::where($this->data, $where);
        if (empty($rows)) {
            return null;
        }
        $keys = array_keys($rows);
        return $keys[0];
    }

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $defaults;

    /**
     * @var array|scalar
     */
    private $pk;

    /**
     * @var int
     */
    private $lastAI;
}
