<?php
/**
 * @package go\DB
 */

namespace go\DB\Implementations;

use go\DB\Implementations\TestBase\Engine;

/**
 * The adapter for test
 * @see \go\DB\Implementations\TestBase\Engine
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class Test extends Base
{
    /**
     * {@inheritdoc}
     */
    public function connect(array $params, &$errorInfo = null, &$errorCode = null)
    {
        if ($params['host'] !== 'localhost') {
            $errorInfo = 'Unable connect to "'.$params['host'].'"';
            $errorCode = Engine::ERROR_CONNECT;
            self::$logs[] = __FUNCTION__.' error';
            return false;
        }
        self::$logs[] = __FUNCTION__;
        return (new Engine());
    }

    /**
     * {@inheritdoc}
     */
    public function close($connection)
    {
        self::$logs[] = __METHOD__;
        $connection->close();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function query($connection, $query)
    {
        self::$logs[] = __FUNCTION__.': '.$query;
        return $connection->query($query);
    }

    /**
     * {@inheritdoc}
     */
    public function getInsertId($connection, $cursor = null)
    {
        return $connection->getInsertId();
    }

    /**
     * {@inheritdoc}
     */
    public function getAffectedRows($connection, $cursor = null)
    {
        return $connection->getAffectedRows();
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorInfo($connection, $cursor = null)
    {
        return $connection->getErrorInfo();
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorCode($connection, $cursor = null)
    {
        return $connection->getErrorCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getNumRows($connection, $cursor)
    {
        return $cursor->getNumRows();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRow($connection, $cursor)
    {
        return $cursor->fetchRow();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAssoc($connection, $cursor)
    {
        return $cursor->fetchAssoc();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchObject($connection, $cursor)
    {
        return $cursor->fetchObject();
    }

    /**
     * {@inheritdoc}
     */
    public function freeCursor($connection, $cursor)
    {
        self::$logs[] = __FUNCTION__;
        return true;
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
        return $cursor->reset();
    }

    /**
     * @return array
     */
    public static function getLogs()
    {
        return self::$logs;
    }

    public static function resetLogs()
    {
        self::$logs = array();
    }

    /**
     * {@inheritdoc}
     */
    protected $paramsReq = array('host');

    /**
     * {@inheritdoc}
     */
    protected $paramsDefault = array('port' => 777);

    /**
     * @var array
     */
    protected static $logs = array();
}
