<?php
/**
 * "Движок" тестовой базы
 *
 * Тестовая база принимает три вида запроса:
 *
 * SELECT <col> FROM <table> [LIMIT i,i]
 *
 * <col> - * или через запятую
 * <table> - единственная существующая таблица: "table"
 * 
 * Все "данные" описаны в $table
 *
 * "столбец" `null` всегда возвращает NULL и не выбирается при SELECT *
 *
 * INSERT
 * просто увеличивает автоинкремент. Ничего в "данные" не добавляет
 * автоикремент каждый раз начинается с начала
 *
 * UPDATE [LIMIT i,i]
 * затрагивает affected rows
 *
 * @package    go\DB
 * @subpackage Implementations
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Implementations\TestBase;

final class Engine
{

    /**
     * Тестовая таблица
     * 
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
    const ERROR_TABLE    = 3;
    const ERROR_COL      = 4;

    /**
     * Выполнения запроса
     *
     * @param string $query
     * @return \go\DB\Implementations\TestBase\Cursor | bool
     */
    public function query($query) {
        $this->errorInfo = null;
        $this->errorCode = null;
        $this->affectedRows = 0;
        $query    = \strtolower($query);
        $query    = \explode(' ', $query, 2);
        $operator = $query[0];
        $query    = isset($query[1]) ? $query[1] : '';
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
     * Закрыть "подключение"
     */
    public function close() {
        $this->closed = true;
        return true;
    }

    /**
     * Закрыто ли подключение
     */
    public function isClosed() {
        return $this->closed;
    }

    /**
     * @return string
     */
    public function getErrorInfo() {
        return $this->errorInfo;
    }

    /**
     * @return int
     */
    public function getErrorCode() {
        return $this->errorCode;
    }

    /**
     * @return int
     */
    public function getInsertId() {
        return $this->lastInsertId;
    }

    /**
     * @return int
     */
    public function getAffectedRows() {
        return $this->affectedRows;
    }

    /**
     * @return \go\DB\Implements\TestBase\Cursor
     */
    private function select($query) {
        $pattern = '~^(.*?)FROM (.*?)(LIMIT (.*?))?$~i';
        if (!preg_match($pattern, $query, $matches)) {
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
    private function insert($query) {
        $this->lastInsertId++;
        return true;
    }

    /**
     * @return bool
     */
    private function update($query) {
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
    private function parseLimit($limit) {
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
}