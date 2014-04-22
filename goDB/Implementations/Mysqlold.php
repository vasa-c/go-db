<?php
/**
 * @package go\DB
 */

namespace go\DB\Implementations;

/**
 * The adapter for php_mysql (deprecated)
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class Mysqlold extends Base
{
    /**
     * {@inheritdoc}
     */
    protected $paramsReq = array('username', 'password');

    /**
     * {@inheritdoc}
     */
    protected $paramsDefault = array(
        'host' => 'localhost',
        'dbname' => null,
        'charset' => null,
    );

    /**
     * {@inheritdoc}
     */
    public function connect(array $params, &$errorInfo = null, &$errorCode = null)
    {
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
     * {@inheritdoc}
     */
    public function close($connection)
    {
        return @\mysql_close($connection);
    }

    /**
     * {@inheritdoc}
     */
    public function query($connection, $query)
    {
        return \mysql_query($query, $connection);
    }

    /**
     * {@inheritdoc}
     */
    public function getInsertId($connection, $cursor = null)
    {
        return \mysql_insert_id($connection);
    }

    /**
     * {@inheritdoc}
     */
    public function getAffectedRows($connection, $cursor = null)
    {
        return \mysql_affected_rows($connection);
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorInfo($connection, $cursor = null)
    {
        return \mysql_error($connection);
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorCode($connection, $cursor = null)
    {
        return \mysql_errno($connection);
    }

    /**
     * {@inheritdoc}
     */
    public function getNumRows($connection, $cursor)
    {
        return \mysql_num_rows($cursor);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRow($connection, $cursor)
    {
        return \mysql_fetch_row($cursor);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAssoc($connection, $cursor)
    {
        return \mysql_fetch_assoc($cursor);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchObject($connection, $cursor)
    {
        return \mysql_fetch_object($cursor);
    }

    /**
     * {@inheritdoc}
     */
    public function freeCursor($connection, $cursor)
    {
        return \mysql_free_result($cursor);
    }

    /**
     * {@inheritdoc}
     */
    public function escapeString($connection, $value)
    {
        return \mysql_real_escape_string($value, $connection);
    }

    /**
     * {@inheritdoc}
     */
    protected function reprField($connection, $value)
    {
        return '`'.$value.'`';
    }

    /**
     * {@inheritdoc}
     */
    public function rewindCursor($connection, $cursor)
    {
        return \mysql_data_seek($cursor, 0);
    }
}
