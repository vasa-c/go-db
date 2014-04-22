<?php
/**
 * @package go\DB
 */

namespace go\DB\Implementations\TestBase;

/**
 * The "engine" of the test database
 *
 * It understands three types of queries
 *
 * 1. SELECT <col> FROM <table> [LIMIT i,i]
 *
 * <col> - * or a comma-separated list
 * <table> - only table "table"
 *
 * All data is defined in $table
 *
 * The "column" `null` always contain NULL (and don't returns in SELECT *)
 *
 * 2. INSERT
 * Just increment autoincrement.
 *
 * 3. UPDATE [LIMIT i,i]
 * It affects to "affected rows" value
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class Engine
{
    /**
     * @var array
     */
    protected $table = array(
        array('a' => 1, 'b' => 2, 'c' => 3),
        array('a' => 2, 'b' => 3, 'c' => 4),
        array('a' => 3, 'b' => 4, 'c' => 5),
        array('a' => 4, 'b' => 4, 'c' => 6),
        array('a' => 5, 'b' => 4, 'c' => 7),
        array('a' => 6, 'b' => 4, 'c' => 8),
    );

    const ERROR_CONNECT  = 1;
    const ERROR_OPERATOR = 2;
    const ERROR_TABLE = 3;
    const ERROR_COL = 4;

    /**
     * Performs a query
     *
     * @param string $query
     * @return \go\DB\Implementations\TestBase\Cursor | bool
     */
    public function query($query)
    {
        $this->errorInfo = null;
        $this->errorCode = null;
        $this->affectedRows = 0;
        $this->log('query: '.$query);
        $query = \strtolower($query);
        $query = \explode(' ', $query, 2);
        $operator = $query[0];
        $query = isset($query[1]) ? $query[1] : '';
        switch ($operator) {
            case 'select':
                return $this->select($query);
            case 'insert':
                return $this->insert($query);
            case 'update':
                return $this->update($query);
            default:
                $this->errorInfo = 'Unknown operator "'.$operator.'"';
                $this->errorCode = self::ERROR_OPERATOR;
                return false;
        }
    }

    /**
     * Closes a "connection"
     */
    public function close()
    {
        $this->log(__FUNCTION__);
        $this->closed = true;
        return true;
    }

    /**
     * Checks if a connection is closed
     *
     * @return bool
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * @return string
     */
    public function getErrorInfo()
    {
        return $this->errorInfo;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return int
     */
    public function getInsertId()
    {
        $this->log(__FUNCTION__);
        return $this->lastInsertId;
    }

    /**
     * @return int
     */
    public function getAffectedRows()
    {
        $this->log(__FUNCTION__);
        return $this->affectedRows;
    }

    /**
     * @return array
     */
    public function getLogs()
    {
        return $this->logs;
    }

    public function resetLogs()
    {
        $this->logs = array();
    }

    /**
     * @return \go\DB\Implements\TestBase\Cursor
     */
    private function select($query)
    {
        $pattern = '~^(.*?)FROM (.*?)(LIMIT (.*?))?$~i';
        if (!\preg_match($pattern, $query, $matches)) {
            $this->errorInfo = 'Error SELECT query "'.$query.'"';
            $this->errorCode = self::ERROR_TABLE;
        }
        $cols  = \trim($matches[1]);
        $table = \trim($matches[2]);
        $limit = isset($matches[4]) ? $matches[4] : null;
        $limit = $this->parseLimit($limit);
        $table = \str_replace('`', '', $table);
        if ($table != 'table') {
            $this->errorInfo = 'Table "'.$table.'" not found';
            $this->errorCode = self::ERROR_TABLE;
            return false;
        }
        if ($cols == '*') {
            $cols = null;
        } else {
            $cols = \explode(',', \str_replace('`', '', $cols));
        }
        $data = array();
        for ($i = $limit['begin']; $i <= $limit['end']; $i++) {
            $row = $this->table[$i];
            if ($cols) {
                $res = array();
                foreach ($cols as $col) {
                    if (isset($row[$col])) {
                        $res[$col] = $row[$col];
                    } elseif ($col == 'null') {
                        $res['null'] = null;
                    } else {
                        $this->errorInfo = 'Unknown column "'.$col.'"';
                        $this->errorCode = self::ERROR_COL;
                        return false;
                    }
                }
            } else {
                $res = $row;
            }
            $data[] = $res;
        }
        return new Cursor($data);
    }

    /**
     * @return bool
     */
    private function insert($query)
    {
        $this->lastInsertId++;
        return true;
    }

    /**
     * @return bool
     */
    private function update($query)
    {
        $query = \explode('limit', $query, 2);
        if (isset($query[1])) {
            $limit = $query[1];
        } else {
            $limit = '';
        }
        $limit = $this->parseLimit($limit);
        $this->affectedRows = $limit['end'] - $limit['begin'] + 1;
        if ($this->affectedRows < 0) {
            $this->affectedRows = 0;
        }
        return true;
    }

    /**
     * @return array
     */
    private function parseLimit($limit)
    {
        $limit = \trim($limit);
        if (empty($limit)) {
            return array(
                'begin' => 0,
                'end'   => \count($this->table) - 1,
            );
        }
        $limit = \explode(',', $limit, 2);
        if (\count($limit) == 2) {
            $begin = (int)$limit[0];
            $end   = $begin + (int)$limit[1] - 1;
        } else {
            $begin = 0;
            $end   = (int)$limit[0] - 1;
        }
        $max = \count($this->table) - 1;
        if ($end > $max) {
            $end = $max;
        }
        return array(
            'begin' => $begin,
            'end'   => $end,
        );
    }

    /**
     * @param string $log
     */
    private function log($log)
    {
        $this->logs[] = $log;
    }

    /**
     * @var string
     */
    private $errorInfo;

    /**
     * @var int
     */
    private $errorCode;

    /**
     * @var int
     */
    private $lastInsertId = 0;

    /**
     * @var int
     */
    private $affectedRows = 0;

    /**
     * @var bool
     */
    private $closed = false;

    /**
     * @var array
     */
    private $logs = array();
}
