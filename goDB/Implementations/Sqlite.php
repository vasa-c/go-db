<?php
/**
 * @package go\DB
 */

namespace go\DB\Implementations;

/**
 * The adapter for php_sqlite3
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class Sqlite extends Base
{
    /**
     * {@inheritdoc}
     */
    protected $paramsReq = array();

    /**
     * {@inheritdoc}
     */
    protected $paramsDefault = array(
        'filename' => ':memory:',
        'flags' => null,
        'encryption_key' => null,
        'mysql_quot' => false,
    );

    /**
     * {@inheritdoc}
     */
    public function connect(array $params, &$errorInfo = null, &$errorCode = null)
    {
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
        return @$connection->query($query);
    }

    /**
     * {@inheritdoc}
     */
    public function getInsertId($connection, $cursor = null)
    {
        return $connection->lastInsertRowID();
    }

    /**
     * {@inheritdoc}
     */
    public function getAffectedRows($connection, $cursor = null)
    {
        return $connection->changes();
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorInfo($connection, $cursor = null)
    {
        return $connection->lastErrorMsg();
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorCode($connection, $cursor = null)
    {
        return $connection->lastErrorCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getNumRows($connection, $cursor)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRow($connection, $cursor)
    {
        return $cursor->fetchArray(\SQLITE3_NUM);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAssoc($connection, $cursor)
    {
        return $cursor->fetchArray(\SQLITE3_ASSOC);
    }

    /**
     * {@inheritdoc}
     */
    public function freeCursor($connection, $cursor)
    {
        return @$cursor->finalize(); // @todo fix (The SQLite3Result object has not been correctly initialised)
    }

    /**
     * {@inheritdoc}
     */
    public function escapeString($connection, $value)
    {
        return $connection->escapeString($value);
    }

    /**
     * {@inheritdoc}
     */
    public function reprString($connection, $value)
    {
        return "'".$this->escapeString($connection, $value)."'";
    }

    /**
     * {@inheritdoc}
     */
    protected function reprField($connection, $value)
    {
        return '"'.$value.'"';
    }

    /**
     * {@inheritdoc}
     */
    public function rewindCursor($connection, $cursor)
    {
        return $cursor->reset();
    }
}
