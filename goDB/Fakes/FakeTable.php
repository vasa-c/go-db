<?php
/**
 * @package go\DB
 */

namespace go\DB\Fakes;

use go\DB\Fakes\Helpers\FakeQueries;
use go\DB\Exceptions\Query;

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
        $this->lastAI = $this->defLastAI($lastAI);
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
        $this->checkPK('INSERT INTO');
        $this->log('INSERT'.($this->lastAI ? (' '.$this->lastAI) : ''));
        return $this->lastAI;
    }

    /**
     * {@inheritdoc}
     */
    public function multiInsert(array $sets)
    {
        $logs = $this->logs;
        foreach ($sets as $set) {
            $this->insert($set);
        }
        $this->logs = $logs;
        $this->log('INSERT MULTI '.count($sets));
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $set)
    {
        $set = array_replace($this->defaults, $set);
        $cur = $this->findByPK($set);
        if ($cur === null) {
            $logs = $this->logs;
            $this->insert($set);
            $this->logs = $logs;
            $this->log('REPLACE INSERT');
            return;
        }
        $this->data[$cur] = $set;
        $this->log('REPLACE UPDATE');
    }

    /**
     * {@inheritdoc}
     */
    public function multiReplace(array $sets)
    {
        $logs = $this->logs;
        foreach ($sets as $set) {
            $this->replace($set);
        }
        $this->logs = $logs;
        $this->log('REPLACE MULTI '.count($sets));
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
        $this->log('UPDATE '.$ar);
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
        $result = new FakeResult($rows);
        $this->log('SELECT '.count($rows));
        return $result;
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
        $ar = count($del);
        $this->log('DELETE '.$ar);
        return $ar;
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
        $this->log('TRUNCATE');
    }

    /**
     * {@inheritdoc}
     */
    public function getCount($col, $where)
    {
        $rows = FakeQueries::where($this->data, $where);
        if (($col === null) || ($col === true)) {
            $count = count($rows);
        } else {
            $count = 0;
            foreach ($rows as $r) {
                if ($r[$col] !== null) {
                    $count += 1;
                }
            }
        }
        $this->log('COUNT '.$count);
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
     * Begin transaction
     */
    public function begin()
    {
        $this->transactions[] = array(
            'data' => $this->data,
            'lastAI' => $this->lastAI,
        );
        $this->log('BEGIN');
    }

    /**
     * Commit transaction
     */
    public function commit()
    {
        if (!empty($this->transactions)) {
            array_pop($this->transactions);
        }
        $this->log('COMMIT');
    }

    /**
     * Rollback transaction
     */
    public function rollback()
    {
        if (!empty($this->transactions)) {
            $t = array_pop($this->transactions);
            $this->data = $t['data'];
            $this->lastAI = $t['lastAI'];
        }
        $this->log('ROLLBACK');
    }

    /**
     * @param string $message
     */
    public function log($message)
    {
        $this->logs[] = $message;
    }

    public function resetLogs()
    {
        $this->logs = array();
    }

    /**
     * @return string[]
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * @param string $query
     * @throws \go\DB\Exceptions\Query
     */
    private function checkPK($query = null)
    {
        $pk = $this->pk;
        if (!$pk) {
            return;
        }
        if (!is_array($pk)) {
            $pk = array($pk);
        }
        $keys = array();
        foreach ($this->data as $row) {
            $key = array();
            foreach ($pk as $k) {
                $key[] = $row[$k];
            }
            $s = serialize($key);
            if (isset($keys[$s])) {
                $message = 'FakeTable duplicate PK '.implode('-', $key);
                throw new Query($query, $message, 1022);
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
     * @param mixed $lastAI
     * @return mixed
     */
    private function defLastAI($lastAI)
    {
        if ($lastAI !== true) {
            return $lastAI;
        }
        $lastAI = 0;
        foreach ($this->data as $row) {
            $lastAI = max($lastAI, $row[$this->pk]);
        }
        return $lastAI;
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

    /**
     * @var array
     */
    private $transactions = array();

    /**
     * @var string[]
     */
    private $logs = array();
}
