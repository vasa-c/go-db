<?php
/**
 * @package go\DB
 */

namespace go\DB\Helpers\Iterators;

use \go\DB\Helpers\Connector;
use \go\DB\Helpers\Fetchers\Base as FetcherBase;

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
     * @param \go\DB\Helpers\Fetchers\Base $ncursor
     *        the aggregator of low-level cursor implementation
     * @param string $key [optional]
     *        a field which used as a key in a result array (numerics array by default)
     */
    public function __construct(Connector $connector, FetcherBase $ncursor, $key = null)
    {
        $this->implementation = $connector->getImplementation();
        $this->connection = $connector->getConnection();
        $this->ncursor = $ncursor;
        $this->cursor = $ncursor->cursor();
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
        if ($this->key !== null) {
            return $this->nextRow[$this->key];
        }
        return $this->pointer;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->pointer++;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        if ($this->pointer !== 0) {
            $this->implementation->rewindCursor($this->connection, $this->cursor);
            $this->pointer = 0;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        $this->nextRow = $this->fetchNextRow();
        return (!empty($this->nextRow));
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->implementation->getNumRows($this->connection, $this->cursor);
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
     * @var \go\DB\Helpers\Fetchers\Base
     */
    protected $ncursor;

    /**
     * @var string|null
     */
    protected $key;

    /**
     * The pointer to the current position
     *
     * @var string
     */
    protected $pointer = 0;

    /**
     * @var mixed
     */
    protected $nextRow;
}
