<?php
/**
 * @package go\DB
 */

namespace go\DB\Helpers\Iterators;

/**
 * The iterator for query result
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
abstract class Base implements \Iterator, \Countable
{
    /**
     * The constructor
     *
     * @param \go\DB\Helpers\Connector $connector
     *        the connector (the connection must be established)
     * @param mixed $cursor
     *        the low-level cursor implementation
     * @param string $key [optional]
     *        a field which used as a key in a result array (numerics array by default)
     */
    public function __construct(\go\DB\Helpers\Connector $connector, $cursor, $key = null)
    {
        $this->implementation = $connector->getImplementation();
        $this->connection = $connector->getConnection();
        $this->cursor = $cursor;
        $this->key = $key;
        $this->pointer = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->nextRow;
    }

    /**
     * {@inheritdoc}
     */

    public function key()
    {
        if (!$this->nextRow) {
            return false;
        }
        if (!\is_null($this->key)) {
            return $this->nextRow[$this->key];
        }
        return $this->pointer;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->nextRow = $this->fetchNextRow();
        $this->pointer++;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->implementation->rewindCursor($this->connection, $this->cursor);
        $this->pointer = 0;
        $this->nextRow = $this->fetchNextRow();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return ($this->nextRow !== false);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->implementation->getNumRows($this->connection, $cursor);
    }

    /**
     * Extracts a next row from the result
     *
     * @return mixed | false
     */
    abstract protected function fetchNextRow();

    /**
     * @var \go\DB\Implementations\Base
     */
    protected $implementation;

    /**
     * @var mixed
     */
    protected $connection;

    /**
     * @var mixed
     */
    protected $cursor;

    /**
     * @var string|null
     */
    protected $key;

    /**
     * The pointer to the current position
     *
     * @var string
     */
    protected $pointer;

    /**
     * @var mixed
     */
    protected $nextRow;
}
