<?php
/**
 * @package go\Db
 */

namespace go\DB\Helpers\Fetchers;

use go\DB\Helpers\Connector;
use go\DB\Helpers\Iterators;
use \go\DB\Exceptions\UnexpectedFetch;

/**
 * The fetcher from a db-cursor
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Cursor extends Base
{
    /**
     * Constructor
     *
     * @param \go\DB\Helpers\Connector $connector
     *        connector (connection must be established)
     * @param mixed $cursor
     *        low-level implementation of the cursor
     */
    public function __construct(Connector $connector, $cursor)
    {
        $this->connector = $connector;
        $this->implementation = $connector->getImplementation();
        $this->connection = $connector->getConnection();
        $this->cursor = $cursor;
        $this->isCursor = $this->implementation->isCursor($this->connection, $cursor);
    }

    /**
     * {@inheritdoc}
     */
    public function free()
    {
        if ((!$this->isFree) && ($this->isCursor)) {
            $this->implementation->freeCursor($this->connection, $this->cursor);
        }
        $this->cursor = false;
        $this->implementation = false;
        $this->connection = false;
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function assoc($param = null)
    {
        $this->requiredCursor(__FUNCTION__);
        $imp    = $this->implementation;
        $conn   = $this->connection;
        $cursor = $this->cursor;
        $result = array();
        if ($param) {
            $row = $imp->fetchAssoc($conn, $cursor);
            while ($row) {
                $result[$row[$param]] = $row;
                $row = $imp->fetchAssoc($conn, $cursor);
            }
        } else {
            $row = $imp->fetchAssoc($conn, $cursor);
            while ($row) {
                $result[] = $row;
                $row = $imp->fetchAssoc($conn, $cursor);
            }
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function numerics($param = null)
    {
        $this->requiredCursor(__FUNCTION__);
        $imp = $this->implementation;
        $conn = $this->connection;
        $cursor = $this->cursor;
        $result = array();
        if (!\is_null($param)) {
            $row = $imp->fetchRow($conn, $cursor);
            while ($row) {
                $result[$row[$param]] = $row;
                $row = $imp->fetchRow($conn, $cursor);
            }
        } else {
            $row = $row = $imp->fetchRow($conn, $cursor);
            while ($row) {
                $result[] = $row;
                $row = $imp->fetchRow($conn, $cursor);
            }
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function objects($param = null)
    {
        $this->requiredCursor(__FUNCTION__);
        $imp = $this->implementation;
        $conn = $this->connection;
        $cursor = $this->cursor;
        $result = array();
        if ($param) {
            $row = $imp->fetchObject($conn, $cursor);
            while ($row) {
                $result[$row->$param] = $row;
                $row = $imp->fetchObject($conn, $cursor);
            }
        } else {
            $row = $imp->fetchObject($conn, $cursor);
            while ($row) {
                $result[] = $row;
                $row = $imp->fetchObject($conn, $cursor);
            }
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function col($param = null)
    {
        $this->requiredCursor(__FUNCTION__);
        $imp = $this->implementation;
        $conn = $this->connection;
        $cursor = $this->cursor;
        $result = array();
        if ($param) {
            $row = $imp->fetchAssoc($conn, $cursor);
            while ($row) {
                $result[] = $row[$param];
                $row = $imp->fetchAssoc($conn, $cursor);
            }
        } else {
            $row = $imp->fetchRow($conn, $cursor);
            while ($row) {
                $result[] = $row[0];
                $row = $imp->fetchRow($conn, $cursor);
            }
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function vars($param = null)
    {
        $this->requiredCursor(__FUNCTION__);
        $imp = $this->implementation;
        $conn = $this->connection;
        $cursor = $this->cursor;
        $result = array();
        $row = $imp->fetchRow($conn, $cursor);
        while ($row) {
            $result[$row[0]] = \array_key_exists('1', $row) ? $row[1] : $row[0];
            $row = $imp->fetchRow($conn, $cursor);
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function iassoc($param = null)
    {
        $this->requiredCursor(__FUNCTION__);
        return (new Iterators\Assoc($this->connector, $this, $param));
    }

    /**
     * {@inheritdoc}
     */
    public function inumerics($param = null)
    {
        $this->requiredCursor(__FUNCTION__);
        return (new Iterators\Numerics($this->connector, $this, $param));
    }

    /**
     * {@inheritdoc}
     */
    public function iobjects($param = null)
    {
        $this->requiredCursor(__FUNCTION__);
        return (new Iterators\Objects($this->connector, $this, $param));
    }

    /**
     * {@inheritdoc}
     */
    public function ivars($param = null)
    {
        $this->requiredCursor(__FUNCTION__);
        return (new Iterators\Vars($this->connector, $this, $param));
    }

    /**
     * {@inheritdoc}
     */
    public function icol($param = null)
    {
        $this->requiredCursor(__FUNCTION__);
        return (new Iterators\Col($this->connector, $this, $param));
    }

    /**
     * {@inheritdoc}
     */
    public function row($param = null)
    {
        $this->requiredCursor(__FUNCTION__);
        return $this->implementation->fetchAssoc($this->connection, $this->cursor) ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function numeric($param = null)
    {
        $this->requiredCursor(__FUNCTION__);
        return $this->implementation->fetchRow($this->connection, $this->cursor) ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function object($param = null)
    {
        $this->requiredCursor(__FUNCTION__);
        return $this->implementation->fetchObject($this->connection, $this->cursor) ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function el($param = null)
    {
        $this->requiredCursor(__FUNCTION__);
        $result = $this->implementation->fetchRow($this->connection, $this->cursor);
        return $result ? $result[0] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function bool($param = null)
    {
        $this->requiredCursor(__FUNCTION__);
        $result = $this->implementation->fetchRow($this->connection, $this->cursor);
        return $result ? (bool)$result[0] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function num($param = null)
    {
        $this->requiredCursor(__FUNCTION__);
        return $this->implementation->getNumRows($this->connection, $this->cursor);
    }

    /**
     * {@inheritdoc}
     */
    public function id($param = null)
    {
        return $this->implementation->getInsertId($this->connection, $this->cursor);
    }

    /**
     * {@inheritdoc}
     */
    public function ar($param = null)
    {
        return $this->implementation->getAffectedRows($this->connection, $this->cursor);
    }

    /**
     * {@inheritdoc}
     */
    public function cursor($param = null)
    {
        return $this->cursor;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return $this->iassoc();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->implementation->getNumRows($this->connection, $this->cursor);
    }

    /**
     * {@inheritdoc}
     */
    protected function requiredCursor($fetch = null)
    {
        if ((!$this->isCursor) || $this->isFree) {
            throw new UnexpectedFetch($fetch);
        }
        return true;
    }

    /**
     * @var mixed
     */
    protected $cursor;

    /**
     * @var \go\DB\Helpers\Connector
     */
    protected $connector;

    /**
     * @var \go\DB\Implementations\Base
     */
    protected $implementation;

    /**
     * @var mixed
     */
    protected $connection;

    /**
     * @var bool
     */
    protected $isCursor;

    /**
     * @var bool
     */
    protected $isFree = false;
}
