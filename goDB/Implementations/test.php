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
     * Обязательные параметров подключения
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
     * @return bool
     */
    public function connect(array $params) {
        if ($params['host'] != 'localhost') {
            $this->errorInfo = 'Unable connect to "'.$params['host'].'"';
            $this->errorCode = TestBase\Engine::ERROR_CONNECT;
            return false;
        }
        $this->connection = new TestBase\Engine();
        return true;
    }

    /**
     * @override Base
     */
    public function close() {
        $this->connection->close();
        return true;
    }

    /**
     * @override Base
     *
     * @param string $query
     * @return mixed
     */
    public function query($query) {
        return $this->connection->query($query);
    }

    /**
     * @override Base
     *
     * @return int
     */
    public function getInsertId() {
        return $this->connection->getInsertId();
    }

    /**
     * @override Base
     *
     * @return int
     */
    public function getAffectedRows() {
        return $this->connection->getAffectedRows();
    }

    /**
     * @override Base
     *
     * @return string
     */
    protected function realErrorInfo() {
        return $this->connection->getErrorInfo();
    }

    /**
     * @override
     *
     * @return int
     */
    protected function realErrorCode() {
        return $this->connection->getErrorCode();
    }

    /**
     * @override
     *
     * @param mixed $cursor
     * @return int
     */
    public function getNumRows($cursor) {
        return $this->connection->getNumRows();
    }

    /**
     * @override
     *
     * @param mixed $cursor
     * @return array|false
     */
    public function fetchRow($cursor) {
        return $cursor->fetchRow();
    }

    /**
     * @override Base
     *
     * @param mixed $cursor
     * @return array|false
     */
    public function fetchAssoc($cursor) {
        return $cursor->fetchAssoc();
    }

    /**
     * @override Base
     *
     * @param mixed $cursor
     * @return object|false
     */
    public function fetchObject($cursor) {
        return $cursor->fetchObject();
    }

    /**
     * @override Base
     *
     * @param mixed $cursor
     */
    public function freeCursor($cursor) {
        return true;
    }

    /**
     * @override Base
     *
     * @param string $value
     * @return string
     */
    protected function reprField($value) {
        return '`'.$value.'`';
    }

    /**
     * Вернуться в начало курсора
     *
     * @param mixed $cursor
     */
    public function rewindCursor($cursor) {
        return $cursor->reset();
    }
}