<?php
/**
 * Надстройка над php_mysql
 *
 * @package    go\DB
 * @subpackage Implementations
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Implementations;

final class mysqlold extends Base
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
        $connection = @\mysql_connect($params['host'], $params['username'], $params['password'], true);
        if (!$connection) {
            $errorInfo = \mysql_error();
            $errorCode = \mysql_errno();
            return false;
        }
        if ($params['dbname']) {
            if (!@\mysql_select_db($params['dbname'], $connection)) {
                $errorInfo = \mysql_error($connection);
                $errorCode = \mysql_errno($connection);
                @\mysql_close($connection);
                return false;
            }
        }
        if ($params['charset']) {
            if (!\mysql_set_charset($params['charset'], $connection)) {
                $errorInfo = \mysql_error($connection);
                $errorCode = \mysql_errno($connection);
                @\mysql_close($connection);
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
        return @\mysql_close($connection);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param string $query
     * @return mixed
     */
    public function query($connection, $query) {
        return \mysql_query($query, $connection);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @return int
     */
    public function getInsertId($connection) {
        return \mysql_insert_id($connection);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @return int
     */
    public function getAffectedRows($connection) {
        return \mysql_affected_rows($connection);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @return string
     */
    public function getErrorInfo($connection) {
        return \mysql_error($connection);
    }


    /**
     * @override Base
     *
     * @param mixed $connection
     * @return int
     */
    public function getErrorCode($connection) {
        return \mysql_errno($connection);
    }


    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return int
     */
    public function getNumRows($connection, $cursor) {
        return \mysql_num_rows($cursor);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return array|false
     */
    public function fetchRow($connection, $cursor) {
        return \mysql_fetch_row($cursor);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return array|false
     */
    public function fetchAssoc($connection, $cursor) {
        return \mysql_fetch_assoc($cursor);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     * @return object|false
     */
    public function fetchObject($connection, $cursor) {
        return \mysql_fetch_object($cursor);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param mixed $cursor
     */
    public function freeCursor($connection, $cursor) {
        return \mysql_free_result($cursor);
    }

    /**
     * @override Base
     *
     * @param mixed $connection
     * @param scalar $value
     * @return string
     */
    public function escapeString($connection, $value) {
        return \mysql_real_escape_string($value, $connection);
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
        return mysql_data_seek($cursor, $connection);
    }    
}