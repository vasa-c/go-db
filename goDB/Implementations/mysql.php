<?php
/**
 * Надстройка над php_mysqli
 *
 * @package    go\DB
 * @subpackage Implementations
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Implementations;

final class mysql extends Base
{
    /**
     * Обязательные параметры подключения
     *
     * @var array
     */
    protected $paramsReq = array('host', 'username', 'password');

    /**
     * Необязательные параметры подключения
     *
     * параметр => значение по умолчанию
     *
     * @var array
     */
    protected $paramsDefault = array(
        'dbname'  => null,
        'charset' => null,
        'port'    => null,
        'socket'  => null,
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
        $host = \explode(':', $params['host'], 2);
        if (!empty($host[1])) {
            $port = $host[1];
        } else {
            $port = $params['port'];
        }
        $host     = $host[0];
        $user     = $params['username'];
        $password = $params['password'];
        $dbname   = $params['dbname'];
        $socket   = $params['socket'];
        $connection = @(new \mysqli($host, $user, $password, $dbname, $port, $socket));
        if ($connection->connect_error) {
            $this->errorInfo = $connection->connect_error;
            $this->errorCode = $connection->connect_errno;
            return false;
        }
        if ($params['charset']) {
            if (!$connection->set_charset($params['charset'])) {
                $this->errorInfo = $connection->error;
                $this->errorCode = $connection->errno;
                $connection->close();
                return false;
            }
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
        return $connection->query($query, \MYSQLI_STORE_RESULT);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return int
     */
    public function getInsertId($connection, $cursor = null) {
        return $connection->insert_id;
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return int
     */
    public function getAffectedRows($connection, $cursor = null) {
        return $connection->affected_rows;
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return string
     */
    public function getErrorInfo($connection, $cursor = null) {
        return $connection->error;
    }


    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor [optional]
     * @return int
     */
    public function getErrorCode($connection, $cursor = null) {
        return $connection->errno;
    }


    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return int
     */
    public function getNumRows($connection, $cursor) {
        return $cursor->num_rows;
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return array|false
     */
    public function fetchRow($connection, $cursor) {
        return $cursor->fetch_row();
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return array|false
     */
    public function fetchAssoc($connection, $cursor) {
        return $cursor->fetch_assoc();
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return object|false
     */
    public function fetchObject($connection, $cursor) {
        return $cursor->fetch_object();
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     */
    public function freeCursor($connection, $cursor) {
        return $cursor->free();
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param scalar $value
     * @return string
     */
    public function escapeString($connection, $value) {
        return $connection->real_escape_string($value);
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
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     */
    public function rewindCursor($connection, $cursor) {
        return $cursor->data_seek(0);
    }
}