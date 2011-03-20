<?php
/**
 * Реализация тестового типа БД
 *
 * @see TestBase\Engine
 *
 * @package    go\DB
 * @subpackage Implementations
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Implementations;

final class test extends Base
{
    /**
     * Обязательные параметры подключения
     *
     * @var array
     */
    protected $paramsReq = array('host');

    /**
     * Необязательные параметры подключения
     *
     * параметр => значение по умолчанию
     *
     * @var array
     */
    protected $paramsDefault = array('port' => 777);

    /**
     * @override Base
     *
     * @param array $params
     * @param string &$errorInfo
     * @param int &$errorCode
     * @return bool
     */
    public function connect(array $params, &$errorInfo = null, &$errorCode = null) {
        if ($params['host'] != 'localhost') {
            $errorInfo = 'Unable connect to "'.$params['host'].'"';
            $errorCode = TestBase\Engine::ERROR_CONNECT;
            return false;
        }
        return (new TestBase\Engine());
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     */
    public function close($connection) {
        $connection->close();
        return true;
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param string $query
     * @return mixed
     */
    public function query($connection, $query) {
        return $connection->query($query);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return int
     */
    public function getInsertId($connection, $cursor = null) {
        return $connection->getInsertId();
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return int
     */
    public function getAffectedRows($connection, $cursor = null) {
        return $connection->getAffectedRows();
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return string
     */
    public function getErrorInfo($connection, $cursor = null) {
        return $connection->getErrorInfo();
    }

    /**
     * @override
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return int
     */
    public function getErrorCode($connection, $cursor = null) {
        return $connection->getErrorCode();
    }

    /**
     * @override
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return int
     */
    public function getNumRows($connection, $cursor) {
        return $cursor->getNumRows();
    }

    /**
     * @override
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return array|false
     */
    public function fetchRow($connection, $cursor) {
        return $cursor->fetchRow();
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return array|false
     */
    public function fetchAssoc($connection, $cursor) {
        return $cursor->fetchAssoc();
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return object|false
     */
    public function fetchObject($connection, $cursor) {
        return $cursor->fetchObject();
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     */
    public function freeCursor($connection, $cursor) {
        return true;
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param string $value
     * @return string
     */
    protected function reprField($connection, $value) {
        return '`'.$value.'`';
    }

    /**
     * Вернуться в начало курсора
     *
     * @param mixed $connection
     * @param mixed $cursor
     */
    public function rewindCursor($connection, $cursor) {
        return $cursor->reset();
    }
}