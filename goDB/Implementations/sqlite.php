<?php
/**
 * Надстройка над sqlite3
 *
 * @package    go\DB
 * @subpackage Implementations
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Implementations;

final class sqlite extends Base
{
    /**
     * Обязательные параметры подключения
     *
     * @var array
     */
    protected $paramsReq = array('filename');

    /**
     * Необязательные параметры подключения
     *
     * параметр => значение по умолчанию
     *
     * @var array
     */
    protected $paramsDefault = array(
        'flags'          => null,
        'encryption_key' => null,
        'mysql_quot'     => false,
    );

    /**
     * @override Base
     *
     * @param array $params
     * @param string & $errroInfo
     * @param int & $errorCode
     * @return mixed
     */
    public function connect(array $params, &$errorInfo = null, &$errorCode = null) {
        $flags = \is_null($params['flags']) ? (\SQLITE3_OPEN_CREATE | \SQLITE3_OPEN_READWRITE) : $params['flags'];
        try {
            $connection = new \SQLite3($params['filename'], $flags, $params['encryption_key']);
        } catch (\Exception $e) {
            $this->errorInfo = $e->getMessage();
            $this->errorCode = $e->getCode();
            return false;
        }
        return $connection;
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     */
    public function close($connection) {
        return $connection->close();
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param string $query
     * @return mixed
     */
    public function query($connection, $query) {
        return @$connection->query($query);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return int
     */
    public function getInsertId($connection, $cursor = null) {
        return $connection->lastInsertRowID();;
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return int
     */
    public function getAffectedRows($connection, $cursor = null) {
        return $connection->changes();
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return string
     */
    public function getErrorInfo($connection, $cursor = null) {
        return $connection->lastErrorMsg();
    }


    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return int
     */
    public function getErrorCode($connection, $cursor = null) {
        return $connection->lastErrorCode();
    }


    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return int
     */
    public function getNumRows($connection, $cursor) { // в sqlite3 нет num_rows
        return 0;
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return array|false
     */
    public function fetchRow($connection, $cursor) {
        return $cursor->fetchArray(\SQLITE3_NUM);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return array|false
     */
    public function fetchAssoc($connection, $cursor) {
        return $cursor->fetchArray(\SQLITE3_ASSOC);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     */
    public function freeCursor($connection, $cursor) {
        return $cursor->finalize();
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param scalar $value
     * @return string
     */
    public function escapeString($connection, $value) {
        return $connection->escapeString($value);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param scalar $value
     * @return string
     */
    public function reprString($connection, $value) {
        return "'".$this->escapeString($connection, $value)."'";
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param string $value
     * @return string
     */
    protected function reprField($connection, $value) {
        return '"'.$value.'"';
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     */
    public function rewindCursor($connection, $cursor) {
        return $cursor->reset(0);
    }
}