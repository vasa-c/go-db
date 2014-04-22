<?php
/**
 * @package go\DB
 */

namespace go\DB\Implementations;

/**
 * The adapter for php_mysqli
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class Mysql extends Base
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
        'port' => null,
        'socket' => null,
    );

    /**
     * {@inheritdoc}
     */
    public function connect(array $params, &$errorInfo = null, &$errorCode = null)
    {
        $host = \explode(':', $params['host'], 2);
        if (!empty($host[1])) {
            $port = $host[1];
        } else {
            $port = $params['port'];
        }
        $host = $host[0];
        $user = $params['username'];
        $password = $params['password'];
        $dbname = $params['dbname'];
        $socket = $params['socket'];
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
     * {@inheritdoc}
     */
    public function close($connection)
    {
        return $connection->close();
    }

    /**
     * {@inheritdoc}
     */
    public function query($connection, $query)
    {
        return $connection->query($query, \MYSQLI_STORE_RESULT);
    }

    /**
     * {@inheritdoc}
     */
    public function getInsertId($connection, $cursor = null)
    {
        return $connection->insert_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAffectedRows($connection, $cursor = null)
    {
        return $connection->affected_rows;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorInfo($connection, $cursor = null)
    {
        return $connection->error;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorCode($connection, $cursor = null)
    {
        return $connection->errno;
    }

    /**
     * {@inheritdoc}
     */
    public function getNumRows($connection, $cursor)
    {
        return $cursor->num_rows;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRow($connection, $cursor)
    {
        return $cursor->fetch_row();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAssoc($connection, $cursor)
    {
        return $cursor->fetch_assoc();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchObject($connection, $cursor)
    {
        return $cursor->fetch_object();
    }

    /**
     * {@inheritdoc}
     */
    public function freeCursor($connection, $cursor)
    {
        return $cursor->free();
    }

    /**
     * {@inheritdoc}
     */
    public function escapeString($connection, $value)
    {
        return $connection->real_escape_string($value);
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
        return $cursor->data_seek(0);
    }
}
