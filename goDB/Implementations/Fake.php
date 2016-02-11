<?php
/**
 * @package go\DB
 */

namespace go\DB\Implementations;

use go\DB\Fakes\FakeEngine;

/**
 * The adapter for fake DB
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class Fake extends Base
{
    /**
     * {@inheritdoc}
     */
    public function connect(array $params, &$errorInfo = null, &$errorCode = null)
    {
        return (new FakeEngine($params));
    }

    /**
     * {@inheritdoc}
     */
    public function close($connection)
    {
        $connection->close();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function query($connection, $query)
    {
        return $connection->query($query);
    }

    /**
     * {@inheritdoc}
     */
    public function getInsertId($connection, $cursor = null)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAffectedRows($connection, $cursor = null)
    {
        return 0;
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
     * {@inheritdoc}
     */
    protected $paramsReq = array('tables');
}
