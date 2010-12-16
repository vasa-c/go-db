<?php
/**
 * Реализация представления результата запроса
 *
 * @package    go\Db
 * @subpackage Helpers
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Helpers;

use go\DB\Implementations\Base as Implementation;
use go\DB\Exceptions as Exceptions;

class Fetcher implements \go\DB\Result
{
    /**
     * Конструктор
     *
     * @param Implementation $implementation
     * @param mixed $cursor
     */
    public function __construct(Implementation $implementation, $cursor) {
        $this->implementation = $implementation;
        $this->cursor         = $cursor;
        $this->isCursor       = $implementation->isCursor($cursor);
    }

    /**
     * Деструктор - очищает результат
     */
    public function __destruct() {
        $this->free();
    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\Fetch
     *
     * @param string $fetch
     * @return mixed
     */
    public function fetch($fetch) {
        $fetch = \explode(':', $fetch, 2);
        $param = isset($fetch[1]) ? \strtolower($fetch[1]) : null;
        $fetch = $fetch[0];
        $fetches = Config::get('fetch');
        if (!isset($fetches[$fetch])) {
            throw new Exceptions\UnknownFetch($fetch);
        }
        return $this->$fetch($param);
    }

    /**
     * @override \go\DB\Result
     */
    public function free() {
        if ((!$this->isFree) && ($this->isCursor)) {
            $this->implementation->freeCursor($this->cursor);
            $this->cursor         = false;
            $this->implementation = false;
        }
        return true;
    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param string $param [optional]
     * @return array
     */
    public function assoc($param = null) {
        $this->requiredCursor(__FUNCTION__);
        $imp    = $this->implementation;
        $cursor = $this->cursor;
        $result = array();
        if ($param) {
            while ($row = $imp->fetchAssoc($cursor)) {
                $result[$row[$param]] = $row;
            }
        } else {
            while ($row = $imp->fetchAssoc($cursor)) {
                $result[] = $row;
            }
        }
        return $result;
    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param int $param [optional]
     * @return array
     */
    public function numerics($param = null) {
        $this->requiredCursor(__FUNCTION__);
        $imp    = $this->implementation;
        $cursor = $this->cursor;
        $result = array();
        if (!is_null($param)) {
            while ($row = $imp->fetchRow($cursor)) {
                $result[$row[$param]] = $row;
            }
        } else {
            while ($row = $imp->fetchRow($cursor)) {
                $result[] = $row;
            }
        }
        return $result;
    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param string $param [optional]
     * @return array
     */
    public function objects($param = null) {
        $this->requiredCursor(__FUNCTION__);
        $imp    = $this->implementation;
        $cursor = $this->cursor;
        $result = array();
        if ($param) {
            while ($row = $imp->fetchObject($cursor)) {
                $result[$row->$param] = $row;
            }
        } else {
            while ($row = $imp->fetchObject($cursor)) {
                $result[] = $row;
            }
        }
        return $result;
    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return array
     */
    public function col($param = null) {
        $this->requiredCursor(__FUNCTION__);
        $imp    = $this->implementation;
        $cursor = $this->cursor;
        $result = array();
        while ($row = $imp->fetchRow($cursor)) {
            $result[] = $row[0];
        }
        return $result;
    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return array
     */
    public function vars($param = null) {
        $this->requiredCursor(__FUNCTION__);
        $imp    = $this->implementation;
        $cursor = $this->cursor;
        $result = array();
        while ($row = $imp->fetchRow($cursor)) {
            $result[$row[0]] = isset($row[1]) ? $row[1] : $row[0];
        }
        return $result;
    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param string $param [optional]
     * @return Iterator
     */
    public function iassoc($param = null) {
        $this->requiredCursor(__FUNCTION__);
        return (new Iterators\assoc($this->implementation, $this->cursor, $param));
    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param int $param [optional]
     * @return Iterator
     */
    public function inumerics($param = null) {
        $this->requiredCursor(__FUNCTION__);
        return (new Iterators\numerics($this->implementation, $this->cursor, $param));
    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param string $param [optional]
     * @return Iterator
     */
    public function iobjects($param = null) {
        $this->requiredCursor(__FUNCTION__);
        return (new Iterators\objects($this->implementation, $this->cursor, $param));
    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return Iterator
     */
    public function ivars($param = null) {
        $this->requiredCursor(__FUNCTION__);
        return (new Iterators\vars($this->implementation, $this->cursor, $param));
    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return Iterator
     */
    public function icol($param = null) {
        $this->requiredCursor(__FUNCTION__);
        return (new Iterators\col($this->implementation, $this->cursor, $param));
    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return array
     */
    public function row($param = null) {
        $this->requiredCursor(__FUNCTION__);
        return $this->implementation->fetchAssoc($this->cursor) ?: null;
    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return array
     */
    public function numeric($param = null) {
        $this->requiredCursor(__FUNCTION__);
        return $this->implementation->fetchRow($this->cursor) ?: null;
    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return object
     */
    public function object($param = null) {
        $this->requiredCursor(__FUNCTION__);
        return $this->implementation->fetchObject($this->cursor) ?: null;
    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return string
     */
    public function el($param = null) {
        $this->requiredCursor(__FUNCTION__);
        $result = $this->implementation->fetchRow($this->cursor);
        return $result ? $result[0] : null;
    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return bool
     */
    public function bool($param = null) {
        $this->requiredCursor(__FUNCTION__);
        $result = $this->implementation->fetchRow($this->cursor);
        return $result ? (bool)$result[0] : null;
    }

    /**
     * @override \go\DB\Result
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @return int
     */
    public function num($param = null) {
        $this->requiredCursor(__FUNCTION__);
        return $this->implementation->getNumRows($this->cursor);
    }

    /**
     * @override \go\DB\Result
     *
     * @return int
     */
    public function id($param = null) {
        return $this->implementation->getInsertId();
    }

    /**
     * @override \go\DB\Result
     *
     * @return int
     */
    public function ar($param = null) {
        return $this->implementation->getAffectedRows();
    }

    /**
     * @override \go\DB\Result
     *
     * @return mixed
     */
    public function cursor($param = null) {
        return $this->cursor;
    }

    /**
     * @override \go\DB\Result
     *
     * @return \Iterator
     */
    public function getIterator() {
        return $this->iassoc();
    }

    /**
     * Указание на то, что данный формат действует только для выборок
     *
     * @throws \go\DB\Exceptions\UnexpectedFetch
     *
     * @param string $fetch [optional]
     */
    protected function requiredCursor($fetch = null) {
        if ((!$this->isCursor) || ($this->isFree)) {
            throw new \go\DB\Exceptions\UnexpectedFetch($fetch);
        }
        return true;
    }

    /**
     * Внутренняя реализация результата (курсора) для конкретного адаптера
     *
     * @var mixed
     */
    protected $cursor;

    /**
     * Внутренняя реализация подключения к базе
     *
     * @var mixed
     */
    protected $implementation;

    /**
     * Является ли $cursor результатом выборки (курсором)
     *
     * (для SELECT - является, для INSERT - нет)
     *
     * @var bool
     */
    protected $isCursor;

    /**
     * Освобождён ли уже результат
     *
     * @var bool
     */
    protected $isFree = false;
}